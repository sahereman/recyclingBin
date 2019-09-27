<?php

namespace App\Admin\Extensions\Ajax;

use Encore\Admin\Admin;

class Ajax_Input_Text_Button
{
    protected $url;

    protected $fields;

    protected $button;

    protected $title;

    public function __construct($url, $fields = array(), $button = '提交', $title = '请输入')
    {
        $this->url = $url;
        $this->fields = $fields;
        $this->button = $button;
        $this->title = $title;
    }

    protected function script()
    {

        $submitConfirm = $this->button . ' ？';
        $confirm = trans('admin.confirm');
        $cancel = trans('admin.cancel');

        return <<<SCRIPT
    
        $('.grid-Ajax_Input_Text_Button-row').unbind('click').click(function() {
        
           
            var data_url = $(this).attr('ajax-url');
            var data = $(this).data();
            data._token = LA.token;
            
            swal.fire({
              title: "$this->title",
              input: 'text',
              showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "$confirm",
                showLoaderOnConfirm: true,
                cancelButtonText: "$cancel",
                preConfirm: function(input) {
                    data.input = input;
                    return new Promise(function(resolve) {
                        $.ajax({
                            method: 'POST',
                            url: data_url,
                            data: data,
                            success: function (data) {
                                $.pjax.reload('#pjax-container');
                                if (typeof data === 'object') {
                                    if (data.status) {
                                        swal(data.message, '', 'success');
                                    } else {
                                        swal(data.message, '', 'error');
                                    }
                                }
                            },
                            error: function(xhr, errorText, errorStatus) {
                                if(xhr.status == 422){
                                    for(var i in xhr.responseJSON.errors)
                                    {
                                        swal(xhr.responseJSON.errors[i].shift(), '', 'error');
                                        break;
                                    }
                                }
                            }
                        });
                    });
                }
            });
            
            
        });

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
        $s_text = "<a class='grid-Ajax_Input_Text_Button-row btn btn-xs btn-primary' style='margin-right:6px' href='javascript:void(0);' ajax-url='{$this->url}' ";
        foreach ($this->fields as $key => $value)
        {
            $s_text .= "data-{$key}='{$value}'";
        }
        $e_text = ">{$this->button}</a>";

        return $s_text . $e_text;
    }

    public function __toString()
    {
        return $this->render();
    }
}