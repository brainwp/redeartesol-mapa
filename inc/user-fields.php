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
            'id'          => 'cidade-estado',
            'label'       => __( 'Cidade/Estado', 'odin' ),
            'type'        => 'text',
            //'description' => __( 'Descrição do campo de text', 'odin' )
        ),
        array(
            'id'          => 'endereco',
            'label'       => __( 'Endereço', 'odin' ),
            'type'        => 'text',
            //'description' => __( 'Descrição do campo de text', 'odin' )
        ),
        array(
           'id'            => 'regiao', // Obrigatório
           'label'         => __( 'Selecione sua região', 'odin' ), // Obrigatório
           'type'          => 'select', // Obrigatório
           'default'       => '', // Opcional
           'options'       => array( // Obrigatório (adicione aque os ids e títulos)
                ''   => __('Selecione','odin'),
                'norte'   => __('Norte','odin'),
                'nordeste'   => __('Nordeste','odin'),
                'centro-oeste'   => __('Centro-oeste','odin'),
                'sul'   => __('Sul','odin'),
                'sudeste'   => __('Sudeste','odin'),
            ),
        ),
        array(
            'id'          => 'telefone',
            'label'       => __( 'Telefone', 'odin' ),
            'type'        => 'text',
            //'description' => __( 'Descrição do campo de text', 'odin' )
        ),
        array(
           'id'            => 'arte_type', // Obrigatório
           'label'         => __( 'Selecione o tipo de Artesol', 'odin' ), // Obrigatório
           'type'          => 'select', // Obrigatório
           'default'       => '', // Opcional
           'options'       => array( // Obrigatório (adicione aque os ids e títulos)
                ''   => __('Selecione','odin'),
                'associacao'   => __('Associação','odin'),
                'individual'   => __('Artesão individual','odin'),
                'indigena'   => __('Indígena','odin'),
                'mestre'   => __('Meste','odin'),
            ),
        ),
        array(
            'id'          => 'link-leia',
            'label'       => __( 'Link Leia mais', 'odin' ),
            'type'        => 'text',
            //'description' => __( 'Descrição do campo de text', 'odin' )
        ),
    )
);