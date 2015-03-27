<?php
/* User Fields */
$adicionais_user_meta = new Odin_User_Meta(
    'adicionais', // Slug/ID do User Meta (obrigatório)
    'Informações do mapa' // Nome do User Meta  (obrigatório)
);
$adicionais_user_meta->set_fields(
    array(
        array(
            'id'          => 'rede-avatar',
            'label'       => 'Avatar',
            'type'        => 'image'
        ),
        array(
            'id'          => 'endereco',
            'label'       => __( 'Endereço', 'odin' ),
            'type'        => 'text',
            //'description' => __( 'Descrição do campo de text', 'odin' )
        ),
        array(
            'id'          => 'telefone',
            'label'       => __( 'Telefone', 'odin' ),
            'type'        => 'text',
            //'description' => __( 'Descrição do campo de text', 'odin' )
        ),
        array(
            'id'          => 'link-leia',
            'label'       => __( 'Link Leia mais', 'odin' ),
            'type'        => 'text',
            //'description' => __( 'Descrição do campo de text', 'odin' )
        ),
    )
);