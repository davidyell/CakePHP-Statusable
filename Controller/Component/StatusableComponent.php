<?php
/**
 * Component to catch and store the routing prefix used
 *
 * @author David Yell <neon1024@gmail.com>
 */
App::uses('Component', 'Controller');

class StatusableComponent extends Component {

    public function startup(Controller $controller) {
        if (isset($controller->request->params['prefix'])) {
            $controller->{$controller->modelClass}->prefix = $controller->request->params['prefix'];
        }
    }

}