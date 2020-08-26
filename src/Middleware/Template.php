<?php

namespace Middleware;

class Template extends Middleware
{
 
    private $_tpl_data = array();

    public function __set($key,$data)
    {
        $this->_tpl_data[$key] = $data;
    }

    public function display($template, $display = true)
    {
        $Scope = new TemplateScope($template,$this->_tpl_data); //Inject into the view
        
        if($display === true) 
        {
            $Scope->Display();
            exit;
        }
        return $Scope;
    }
    
}