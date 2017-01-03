# 1.0.1-beta

Mapeado os parâmetros da rota, com os parâmetros da _action_:

    /post/:date/:slug
    function postagens($slug, $date) { ... }

    :date -> $date
    :slug -> $slug

Passado a execução da Rota para a classe \Core\Dispatch