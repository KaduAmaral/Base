<?php

namespace Core\Routes\Rules;

use \Core\Request;
use \Core\Route;

interface RuleInterface {
    /**
     *
     * Verifica se a requisição é compatível com a rota.
     *
     * @param Request $request Requisição HTTP.
     *
     * @param Route $route Rota.
     *
     * @return bool TRUE quando sucesso, FALSE quando falhar.
     *
     */
    public function __invoke(Request $request, Route $route);
}