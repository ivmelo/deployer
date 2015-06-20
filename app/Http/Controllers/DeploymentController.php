<?php

namespace App\Http\Controllers;

use App\Command;
use App\Deployment;
use App\Http\Controllers\Controller;
use App\Jobs\QueueDeployment;
use App\Repositories\Contracts\DeploymentRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\ServerLog;
use Input;
use Lang;

/**
 * The controller for showing the status of deployments.
 */
class DeploymentController extends Controller
{
    /**
     * The project repository.
     *
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * Class constructor.
     *
     * @param  ProjectRepositoryInterface $projectRepository
     * @return void
     */
    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * The details of an individual project.
     *
     * @param  int                           $project_id
     * @param  DeploymentRepositoryInterface $deploymentRepository
     * @return View
     */
    public function project($project_id, DeploymentRepositoryInterface $deploymentRepository)
    {
        $project = $this->projectRepository->getById($project_id);

        $optional = $project->commands->filter(function (Command $command) {
            return $command->optional;
        });

        return view('projects.details', [
            'title'         => $project->name,
            'deployments'   => $deploymentRepository->getLatest($project),
            'today'         => $deploymentRepository->getTodayCount($project),
            'last_week'     => $deploymentRepository->getLastWeekCount($project),
            'project'       => $project,
            'servers'       => $project->servers,
            'notifications' => $project->notifications,
            'notifyEmails'  => $project->notifyEmails,
            'heartbeats'    => $project->heartbeats,
            'sharedFiles'   => $project->sharedFiles,
            'projectFiles'  => $project->projectFiles,
            'checkUrls'     => $project->checkUrls,
            'optional'      => $optional,
            'route'         => 'commands',
        ]);
    }

    /**
     * Show the deployment details.
     *
     * @param  Deployment $deployment
     * @return Response
     */
    public function show(Deployment $deployment)
    {
        $output = [];
        foreach ($deployment->steps as $step) {
            foreach ($step->servers as $server) {
                $server->server;

                $server->runtime = (!$server->runtime() ? null : $server->getPresenter()->readable_runtime);
                $server->output  = ((is_null($server->output) || !strlen($server->output)) ? null : '');

                $output[] = $server;
            }
        }

        $project = $deployment->project;

        return view('deployment.details', [
            'breadcrumb' => [
                ['url' => url('projects', $project->id), 'label' => $project->name],
            ],
            'title'      => Lang::get('deployments.details'),
            'project'    => $project,
            'deployment' => $deployment,
            'output'     => json_encode($output), // PresentableInterface does not correctly json encode the models
        ]);
    }

    /**
     * Adds a deployment for the specified project to the queue.
     *
     * @param  int      $project
     * @return Response
     */
    public function deploy($project_id)
    {
        $project = $this->projectRepository->getById($project_id);

        if ($project->servers->where('deploy_code', true)->count() === 0) {
            return redirect()->url('projects', $project->id);
        }

        $deployment         = new Deployment;
        $deployment->reason = Input::get('reason');

        if (Input::has('source') && Input::has('source_' . Input::get('source'))) {
            $deployment->branch = Input::get('source_' . Input::get('source'));
        }

        if (empty($deployment->branch)) {
            $deployment->branch = $project->branch;
        }

        $optional = [];

        if (Input::has('optional')) {
            $optional = Input::get('optional');
        }

        $this->dispatch(new QueueDeployment(
            $project,
            $deployment,
            $optional
        ));

        return redirect()->route('deployment', [
            'id' => $deployment->id,
        ]);
    }

    /**
     * Gets the log output of a particular deployment step.
     *
     * @param  ServerLog $log
     * @return ServerLog
     */
    public function log(ServerLog $log)
    {
        $log->runtime = (!$log->runtime() ? null : $log->getPresenter()->readable_runtime);

        return $log;
    }
}
