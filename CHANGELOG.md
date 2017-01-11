# 1.1.0-beta

Implementado Rotas por Annotation

    class MainController extends Controller {
    
       /**
        * @Route("/", "name":"home")
        */
        public function index() {
           return 'Home';
        }
        
        /**
         * @Route("/contato", "name":"contato")
        public function contato() {
           return 'Contato';
        }
        
        /**
         * @Route("/post/:slug", "name":"postagem", "params":{"slug":"[a-zA-Z0-9\-_ ]"})
        public function contato() {
           return 'Contato';
        }
    
    }
    
É necessário excluir o arquivo cache de rotas, quando for alterado alguma rota por annotation: `APP_DIR/routes.cache.php`

# 1.0.1-beta

Mapeado os parâmetros da rota, com os parâmetros da _action_:

    /post/:date/:slug
    function postagens($slug, $date) { ... }

    :date -> $date
    :slug -> $slug

Passado a execução da Rota para a classe \Core\Dispatch
