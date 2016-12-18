<?php
/**
 * Created by PhpStorm.
 * User: damian
 * Date: 10.12.14
 * Time: 11:59
 */

namespace Aniart\Main\Ajax;

/**
 * Class AbstractAjaxHandler
 * @package Aniart\Main\Ajax
 */
abstract class AbstractAjaxHandler implements AjaxHandlerInterface
{
    protected $post;
    protected $get;
    protected $request;
    protected $response;

    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;
        $this->request = $_REQUEST;
    }

    /**
     * Метод должен возвращать название функции, которая будет вызвана в контексте этого класса, например:
     * <code>
     * {
     *      return $this->post['func'];
     * }
     * </code>
     *
     * @return string
     */
    protected function getFunction()
    {
    	return $this->post['func'];
    }

    public function setError($message)
    {
        $this->response = json_encode(array('status' => 'error', 'message' => $message));
        return false;
    }

    public function setOK($data = array())
    {
        $this->response = json_encode(array('status' => 'ok', 'data' => $data));
        return true;
    }

    /**
     * Запускает обработчик
     */
    public function start()
    {
        $function = $this->getFunction();
        if(!empty($function) && method_exists($this, $function)){
            $this->{$function}();
            echo $this->response;
        }
    }
} 