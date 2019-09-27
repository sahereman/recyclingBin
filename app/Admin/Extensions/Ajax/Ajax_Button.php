<?php

namespace App\Admin\Extensions\Ajax;

use Encore\Admin\Admin;

class Ajax_Button
{
    protected $url;

    protected $fields;

    protected $button;

    public function __construct($url, $fields = array(), $button = '提交')
    {
        $this->url = $url;
        $this->fields = $fields;
        $this->button = $button;
    }

    protected function script()
    {

        $submitConfirm = $this->button . ' ？';
        $confirm = trans('admin.confirm');
        $cancel = trans('admin.cancel');

        return <<<SCRIPT
    
        $('.grid-Ajax_Button-row').unbind('click').click(function() {
        
           
            var data_url = $(this).attr('ajax-url');
            var data = $(this).data();
            data._token = LA.token;
            
            swal({
                title: "$submitConfirm",
                type: "question",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "$confirm",
                showLoaderOnConfirm: true,
                cancelButtonText: "$cancel",
                preConfirm: function() {
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
        $s_text = "<a class='grid-Ajax_Button-row btn btn-xs btn-primary' style='margin-right:6px' href='javascript:void(0);' ajax-url='{$this->url}' ";
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