<?php
namespace app\model;
use app\Common;

/**
 * 区块
 */
class BlockModel extends ModelModel
{
    private $BlockTypeModel = null;     // 区块类型

    protected $config = null;           // 配置信息
    protected $filter = null;           // 过滤器信息

    /**
     * 默认的一些非 空字符串 的设置
     * 用来存在放在空的数据对象中
     */
    protected $data = [
        'config'    => '[]',
        'filter'    => '[]',
    ];

    public function getConfigAttr()
    {
        return json_decode($this->getData('config'));
    }

    public function getFilterAttr()
    {
        return json_decode($this->getData('filter'));
    }

    /**
     * 区域:模块 = n:1
     */
    public function BlockTypeModel()
    {
        if (null === $this->BlockTypeModel) {
            $map = [];
            $map['name'] = $this->getData('block_type_name');
            $this->BlockTypeModel = BlockTypeModel::get($map);
        }

        return $this->BlockTypeModel;
    }

    public function getConfig()
    {
        if (null === $this->config)
        {
            $this->config = Common::configMerge($this->BlockTypeModel()->config, $this->getConfigAttr());
        }

        return $this->config;
    }


    public function getFilter()
    {
        if (null === $this->filter)
        {
            $this->filter = Common::configMerge($this->BlockTypeModel->filter, $this->getFilterAttr());
        }

        return $this->filter;
    }

    /**
     * 获取某个position下的所有 启用 的区载信息
     * @param  string $name position名称
     * @return lists       BlockModels
     */
    public function getActiveListsByPositionName($name)
    {
        $map = ['position_name' => $name, 'status' => '0'];
        $order = ['weight' => 'desc'];
        $BlockModels = $this->where($map)->order($order)->select();
        foreach ($BlockModels as $key => &$BlockModel)
        {
            // 去除没有权限显示的区块
            if (!$BlockModel->isShowInCurrentMenu())
            {
                unset($BlockModels[$key]);
            }
        }
        return $BlockModels;
    }


    /**
     * 判断当前BLOCK是否在 正在访问的当前菜单 中显示
     * @return boolean 
     */
    public function isShowInCurrentMenu()
    {
        // 取出当前菜单
        $currentMenuModel = MenuModel::getCurrentMenuModel();

        // 判断当前菜单是否拥有此block的显示权限
        $map = ['block_id'=>$this->id, 'menu_id' => $currentMenuModel->id];
        $AccessBlockMenuModel = AccessMenuBlockModel::get($map);
        if (0 === $AccessBlockMenuModel->getData('id'))
        {
            return false;
        } else {
            return true;
        }
    }

    public function checkIsShow(MenuModel &$MenuModel)
    {
        $map = [];
        $map['block_id']    = $this->data['id'];
        $map['menu_id']     = $MenuModel->getData('id');
        if (null === AccessMenuBlockModel::get($map))
        {
            return false;
        } else {
            return true;
        }
    }
}