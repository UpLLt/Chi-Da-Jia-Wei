<?php  
class EmptyController extends Controller{  
  
    //�������ģ�鲻����ʱ��Ĭ��ִ�е�ģ��  
    public function index(){  
        header("HTTP/1.0 404 Not Found");//404״̬��  
        $this->display("Public:404"); //��ʾ�Զ����404ҳ��ģ��  
    }  
      
    function _empty(){  
        header("HTTP/1.0 404 Not Found");//404״̬��  
        $this->display("Public:404");//��ʾ�Զ����404ҳ��ģ��  
    }  
}  
?> 