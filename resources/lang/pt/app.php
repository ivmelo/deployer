<?php

return [

    'name'              => 'Deployer',
    'signout'           => 'Sair',
    'toggle_nav'        => 'Ativar/Desativar Navegação',
    'dashboard'         => 'Dashboard',
    'admin'             => 'Administração',
    'projects'          => 'Projetos',
    'templates'         => 'Templates',
    'groups'            => 'Grupos',
    'users'             => 'Usuarios',
    'created'           => 'Criado',
    'edit'              => 'Editar',
    'confirm'           => 'Confirmar',
    'not_applicable'    => 'N/A',
    'date'              => 'Data',
    'status'            => 'Status',
    'details'           => 'Detalhes',
    'delete'            => 'Delete',
    'save'              => 'Salvar',
    'close'             => 'Fechar',
    'never'             => 'Nunca',
    'none'              => 'Nenhum',
    'yes'               => 'Sim',
    'no'                => 'Não',
    'warning'           => 'AVISO',
    'socket_error'      => 'Erro no servidor',
    'socket_error_info' => 'A conecxão não pode ser estabelecida com o socket em' .
                           '<strong>' . config('deployer.socket_url') . '</strong>. Isso é necessário para reportar ' .
                           'o status de deployments sendo executados. Por favor, recarregue. Se o problema persistir ' .
                           'contate o administrador do sistema',
//    'not_down'          => 'You must switch to maintenance mode before running this command, this will ensure that ' .
//                           'no new deployments are started',
//    'switch_down'       => 'Switch to maintenance mode now? The app will switch back to live mode once cleanup ' .
//                           'is finished',
    'update_available'  => 'Uma nova atualização está disponível!',
    'outdated'          => 'Você está usando uma versão ultrapassada :current, existe uma existe uma versão mais recente ' .
                           '<a href=":link" rel="noreferrer">:latest</a> disponível!',

];
