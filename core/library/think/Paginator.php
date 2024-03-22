<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zhangyajun <448901948@qq.com>
// +----------------------------------------------------------------------

namespace think;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

abstract class Paginator implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    /** @var bool 是否为简洁模式 */
    protected $simple = false;

    /** @var Collection 数据集 */
    protected $items;

    /** @var integer 当前页 */
    public $currentPage;

    /** @var  integer 最后一页 */
    public $lastPage;

    /** @var integer|null 数据总数 */
    public $total;

    /** @var  integer 每页的数量 */
    protected $listRows;

    /** @var bool 是否有下一页 */
    public $hasMore;

    /** @var array 一些配置 */
    protected $options = [
        'var_page' => 'page',
        'path'     => '/',
        'query'    => [],
        'fragment' => '',
    ];

    /** @var mixed simple模式下的下个元素 */
    protected $nextItem;

    public function __construct($items, $listRows, $currentPage = null, $total = null, $simple = false, $options = [])
    {
        $this->options = array_merge($this->options, $options);

        $this->options['path'] = '/' != $this->options['path'] ? rtrim($this->options['path'], '/') : $this->options['path'];

        $this->simple   = $simple;
        $this->listRows = $listRows;

        if (!$items instanceof Collection) {
            $items = Collection::make($items);
        }

        if ($simple) {
            $this->currentPage = $this->setCurrentPage($currentPage);
            $this->hasMore     = count($items) > ($this->listRows);
            if ($this->hasMore) {
                $this->nextItem = $items->slice($this->listRows, 1);
            }
            $items = $items->slice(0, $this->listRows);
        } else {
            $this->total       = $total;
            $this->lastPage    = (int) ceil($total / $listRows);
            $this->currentPage = $this->setCurrentPage($currentPage);
            $this->hasMore     = $this->currentPage < $this->lastPage;
        }
        $this->items = $items;
    }

    /**
     * @param       $items
     * @param       $listRows
     * @param null  $currentPage
     * @param bool  $simple
     * @param null  $total
     * @param array $options
     * @return Paginator
     */
    public static function make($items, $listRows, $currentPage = null, $total = null, $simple = false, $options = [])
    {
        return new static($items, $listRows, $currentPage, $total, $simple, $options);
    }

    protected function setCurrentPage($currentPage)
    {
        if (!$this->simple && $currentPage > $this->lastPage) {
            return $this->lastPage > 0 ? $this->lastPage : 1;
        }

        return $currentPage;
    }

    /**
     * 获取页码对应的链接
     *
     * @param $page
     * @return string
     */
    protected function url($page)
    {
        // tag标签ID by 小虎哥
        static $tagid = 0;
        if (!empty($this->options['query']['tagid'])) {
            $tagid = intval($this->options['query']['tagid']);
        }

        // 栏目ID by 小虎哥
        static $typeid = 0;
        if (!empty($this->options['query']['typeid_tmp'])) {
            $typeid = intval($this->options['query']['typeid_tmp']);
            unset($this->options['query']['typeid_tmp']);
        }

        if ($page <= 0) {
            $page = 1;
        }

        if (strpos($this->options['path'], '[PAGE]') === false) {
            $parameters = [$this->options['var_page'] => $page];
            $path       = $this->options['path'];
        } else {
            $parameters = [];
            $path       = str_replace('[PAGE]', $page, $this->options['path']);
        }
        if (count($this->options['query']) > 0) {
            $parameters = array_merge($this->options['query'], $parameters);
        }
        $url = $path;

        /*-----------------URL模式------------------*/
        $tags_html = 0;
        $tags_seo_pseudo = 0;
        if (is_dir('./weapp/Tags/')) {
            $tags_html = config('tpcache.plus_tags_html');
            if (!empty($tags_html)) {
                $tagsModel = new \weapp\Tags\model\TagsModel;
                $tagsConfData = $tagsModel->getWeappData();
                $tags_seo_pseudo = empty($tagsConfData['data']['seo_pseudo']) ? 2 : intval($tagsConfData['data']['seo_pseudo']);
            }
        }
        // URL模式
        static $seo_pseudo = null;
        null === $seo_pseudo && $seo_pseudo = config('ey_config.seo_pseudo');
        // 筛选标识
        static $url_screen_var = null;
        null === $url_screen_var && $url_screen_var = config('global.url_screen_var');
        if (3 == $seo_pseudo) { // 伪静态模式 by 小虎哥
            if (!isset($this->options['query'][$url_screen_var])) { // 不是筛选URL
                // 栏目分页
                if (!empty($typeid)) {
                    if (stristr($url, '.html')) {
                        if (1 >= $this->currentPage) {
                            1 < $page && $url = preg_replace('/\.html$/i', "/list_{$typeid}_{$page}.html", $url);
                        } else {
                            $url = preg_replace('/\/list_'.$typeid.'_(\d+)\.html$/i', '.html', $url);
                            1 < $page && $url = preg_replace('/\.html$/i', "/list_{$typeid}_{$page}.html", $url);
                        }
                    } else {
                        $url .= '/';
                        if (1 >= $this->currentPage) {
                            1 < $page && $url .= "list_{$typeid}_{$page}/";
                        } else {
                            $url = preg_replace('/\/list_'.$typeid.'_(\d+)\/$/i', '/', $url);
                            1 < $page && $url .= "list_{$typeid}_{$page}/";
                        }
                    }
                    unset($parameters[$this->options['var_page']]);
                }
            }
            /*--end*/
        }
        /*------------------------end*/

        // tag静态化插件与内置的tag伪静态路由通用
        $ctl_act = MODULE_NAME.'@'.CONTROLLER_NAME.'@'.ACTION_NAME;
        if (($ctl_act != 'plugins@Tags@buildpclists' && 2 == $tags_seo_pseudo) || 3 == $tags_seo_pseudo || (3 == $seo_pseudo && empty($tags_html))) {
            if (!isset($this->options['query'][$url_screen_var])) { // 不是筛选URL
                // tag标签分页
                if (!empty($tagid)) {
                    if (1 >= $this->currentPage) {
                        1 < $page && $url = preg_replace('/\.html$/i', "_{$page}.html", $url);
                    } else {
                        $url = preg_replace('/\/'.$tagid.'_(\d+)\.html$/i', "/{$tagid}.html", $url);
                        1 < $page && $url = preg_replace('/\.html$/i', "_{$page}.html", $url);
                    }
                    unset($parameters[$this->options['var_page']]);
                    unset($parameters['tagid']);
                    unset($this->options['query']['tagid']);
                }
            }
        }

        if (empty($tags_html)) {
            if (2 != $seo_pseudo && 1 == $page) { // 排除静态页面模式
                unset($parameters[$this->options['var_page']]);
            }
        }

        if (!empty($parameters)) {
            if (!stristr($url, 'index.php') && !stristr($url, '.html')) {
                $url = rtrim($url, '/').'/';
            }
            $url .= '?' . http_build_query($parameters, null, '&');
        }
        $url = get_absolute_url($url,"url");
        return $url . $this->buildFragment();
    }

    /**
     * 自动获取当前页码
     * @param string $varPage
     * @param int    $default
     * @return int
     */
    public static function getCurrentPage($varPage = 'page', $default = 1)
    {
        $page = (int) Request::instance()->param($varPage);

        if (filter_var($page, FILTER_VALIDATE_INT) !== false && $page >= 1) {
            return $page;
        }

        return $default;
    }

    /**
     * 自动获取当前的path
     * @return string
     */
    public static function getCurrentPath()
    {
        return Request::instance()->baseUrl();
    }

    public function total()
    {
        if ($this->simple) {
            throw new \DomainException('not support total');
        }
        return $this->total;
    }

    public function listRows()
    {
        return $this->listRows;
    }

    public function currentPage()
    {
        return $this->currentPage;
    }

    public function lastPage()
    {
        if ($this->simple) {
            throw new \DomainException('not support last');
        }
        return $this->lastPage;
    }

    /**
     * 数据是否足够分页
     * @return boolean
     */
    public function hasPages()
    {
        return !(1 == $this->currentPage && !$this->hasMore);
    }

    /**
     * 创建一组分页链接
     *
     * @param  int $start
     * @param  int $end
     * @return array
     */
    public function getUrlRange($start, $end)
    {
        $urls = [];

        for ($page = $start; $page <= $end; $page++) {
            $urls[$page] = $this->url($page);
        }

        return $urls;
    }

    /**
     * 设置URL锚点
     *
     * @param  string|null $fragment
     * @return $this
     */
    public function fragment($fragment)
    {
        $this->options['fragment'] = $fragment;
        return $this;
    }

    /**
     * 添加URL参数
     *
     * @param  array|string $key
     * @param  string|null  $value
     * @return $this
     */
    public function appends($key, $value = null)
    {
        if (!is_array($key)) {
            $queries = [$key => $value];
        } else {
            $queries = $key;
        }

        foreach ($queries as $k => $v) {
            if ($k !== $this->options['var_page']) {
                $this->options['query'][$k] = $v;
            }
        }

        return $this;
    }

    /**
     * 构造锚点字符串
     *
     * @return string
     */
    protected function buildFragment()
    {
        return $this->options['fragment'] ? '#' . $this->options['fragment'] : '';
    }

    /**
     * 渲染分页html
     * @return mixed
     */
    abstract public function render();

    public function items()
    {
        return $this->items->all();
    }

    public function getCollection()
    {
        return $this->items;
    }

    public function isEmpty()
    {
        return $this->items->isEmpty();
    }

    /**
     * 给每个元素执行个回调
     *
     * @param  callable $callback
     * @return $this
     */
    public function each(callable $callback)
    {
        foreach ($this->items as $key => $item) {
            $result = $callback($item, $key);
            if (false === $result) {
                break;
            } elseif (!is_object($item)) {
                $this->items[$key] = $result;
            }
        }

        return $this;
    }

    /**
     * Retrieve an external iterator
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items->all());
    }

    /**
     * Whether a offset exists
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->items->offsetExists($offset);
    }

    /**
     * Offset to retrieve
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->items->offsetGet($offset);
    }

    /**
     * Offset to set
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->items->offsetSet($offset, $value);
    }

    /**
     * Offset to unset
     * @param mixed $offset
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->items->offsetUnset($offset);
    }

    /**
     * Count elements of an object
     */
    public function count()
    {
        return $this->items->count();
    }

    public function __toString()
    {
        return (string) $this->render();
    }

    public function toArray()
    {
        if ($this->simple) {
            return [
                'per_page'     => $this->listRows,
                'current_page' => $this->currentPage,
                'has_more'     => $this->hasMore,
                'next_item'    => $this->nextItem,
                'data'         => $this->items->toArray(),
            ];
        } else {
            return [
                'total'        => $this->total,
                'per_page'     => $this->listRows,
                'current_page' => $this->currentPage,
                'last_page'    => $this->lastPage,
                'data'         => $this->items->toArray(),
            ];
        }

    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __call($name, $arguments)
    {
        $collection = $this->getCollection();

        $result = call_user_func_array([$collection, $name], $arguments);

        if ($result === $collection) {
            return $this;
        }

        return $result;
    }

}
