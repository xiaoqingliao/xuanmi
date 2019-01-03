<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Services;

class Pager
{
    private $count;
    private $currentPage;
    private $totalPage;
    private $pagesize;
    private $url;
    private $param;

    public function __construct($count, $page, $pagesize)
    {
        $this->count = $count;
        $this->currentPage = $page;
        $this->pagesize = $pagesize;
        $this->totalPage = ceil($count / $pagesize);
        $this->url = request()->url();
        $this->param = [];
    }
    
    public function appends($params)
    {
        $this->param = array_merge($this->param, $params);
        return $this;
    }

    private function getUrl($page) {
        $params = $this->param;
        $params['page'] = $page;

        return $this->url . '?' . http_build_query($params);
    }

    public function render()
    {
        if ($this->totalPage <= 1) return '';

        $first_page = '<li><a href="'. $this->getUrl(1) .'">1</a></li>';
        $prev_page = '<li><a href="'. $this->getUrl($this->currentPage - 1) .'">«</a></li>';
        if ($this->currentPage == 1) {
            $first_page = '<li class="active"><span>1</span></li>';
            $prev_page = '<li class="disabled"><span>«</span></li>';
        }
        
        $next_page = '<li><a href="'. $this->getUrl($this->currentPage + 1) .'">»</a></li>';
        $last_page = '<li><a href="'. $this->getUrl($this->totalPage) .'">'. $this->totalPage .'</a></li>';
        if ($this->currentPage == $this->totalPage) {
            $last_page = '<li class="active"><span>'. $this->totalPage .'</span></li>';
            $next_page = '<li class="disabled"><span>«</span></li>';
        }
        
        $middle_page = '';
        $prev_span = '';
        $next_span = '';

        $start = $this->currentPage - 2;
        if ($start <= 1) {
            $start = 2;
        }
        $end = $start + 4;
        if ($end >= $this->totalPage) {
            $end = $this->totalPage - 1;
        }
        if ($start > 2) {
            $prev_span = '<li class="disabled"><span>...</span></li>';
        }
        if ($end < $this->totalPage - 1) {
            $next_span = '<li class="disabled"><span>...</span></li>';
        }
        
        for($idx = $start; $idx<=$end; $idx++) {
            if ($idx == $this->currentPage) {
                $middle_page .= '<li class="active"><span>'. $idx .'</span></li>';
            } else {
                $middle_page .= '<li><a href="'. $this->getUrl($idx) .'">'. $idx .'</a></li>';
            }
        }

        $html = '<ul class="pagination">' . $prev_page . $first_page . $prev_span . $middle_page . $next_span . $last_page . $next_page . '</ul>';
        return $html;
    }
}