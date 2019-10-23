<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class MenuList extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'menu_list';
    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * 模型的连接名称
     *
     * @var string
     */
    protected $connection = 'mysql';
}