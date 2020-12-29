<?php
    /**
     * 基于sqli-labs-master的sql注入漏洞扫描测试
     */
    include_once("simplehtmldom/simple_html_dom.php");
    ini_set('max_execution_time', '0');//设置为0，就是不限制执行的时间
    //去除数组中重复元素
    function arrUnique($arr)
    {
        $arr_result = array();
        foreach($arr as $k=>$v)
        {
            if(!in_array($v,$arr_result))
            {
                $arr_result[$k] = $v; 
            }
        }
        return $arr_result;
    }
    #获得网站所有的链接
    function getAllLinks($url)
    {
        $urlLen=strlen($url);
        #判断url最后一个字符是否是/
        if($url[$urlLen-1]!='/')
            $url+='/';
        $html = file_get_html($url);
        $inallLinks=array();
        // 寻找所有连接
        foreach($html->find('a') as $e) 
        {
            $inallLinks[]=$url.$e->href.'/';
            $area=file_get_html($url.$e->href);
            foreach($area->find('area') as $er)
            {
                #echo 'http://localhost/sqli-labs-master/'. $er->href.'/' .'<br>';
                $inallLinks[]=$url. $er->href.'/';
            }
        }
        $inallLinks=arrUnique($inallLinks);
        return $inallLinks;
    }
    $stime=microtime(true); //获取程序开始执行的时间
    #获得去重复的网站所有链接
    $allLinks=getAllLinks('http://localhost/sqli-labs-master/');
    echo '正在注入测试中！！<br>';
    $hasInjectLinks=array();
    $sum=0;
    foreach($allLinks as $key=>$value)
    {
        $url=$value.'?id=1';
        exec("C:\Python39\python.exe C:/Python39/sqlmap/sqlmap.py -u $url",$array);
        $info=implode('<br>',$array);
        if(strpos($info,'might be injectable'))
        {
            $hasInjectLinks[]=$value;
        }
        unset($array);
    }
    echo '有SQL注入漏洞的页面如下,总共有'.count($hasInjectLinks).'条链接!!<br>';
    for($i=0;$i<count($hasInjectLinks);$i++)
    {
        echo $hasInjectLinks[$i].'<br>';
    }
    $etime=microtime(true);//获取程序执行结束的时间
    $total=round(($etime-$stime),6);   //计算差值，保留六位有效位
    echo "SQL注入程序执行时间：{$total}秒"; 
    #foreach($hasInjectLinks as $key=>$value)
    #    echo $value.'<br>';
    /*$url="http://localhost/sqli-labs-master/Less-9/?id=1";
    //$url="http://localhost/sqli-labs-master/#fm_imagemap/?id=1";
    exec("C:\Python39\python.exe C:/Python39/sqlmap/sqlmap.py -u $url",$array);
    var_dump($array);
    /*$info=implode('<br>',$array);
    $label1='might be injectable';
    $label2="boolean-based blind - WHERE or HAVING clause' injectable";
    echo $info;
    if(strpos($info,$label1)||strpos($info,$label1))
        echo '该页面有注入<br>';
    else
    echo '该页面不存在注入<br>';*/
    

?>