<?php


namespace App\Console\Commands;


use App\Models\DeptModel;
use App\Services\DingTalkService;
use Exception;
use Illuminate\Console\Command;

class UpdateDept extends Command
{
    protected $signature = 'command:updateDept';

    protected $description = '更新部门';

    /**
     * @var DingTalkService
     */
    protected $ddService;

    public function __construct(DingTalkService $ddService)
    {
        parent::__construct();
        $this->ddService = $ddService;
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        $deptIds = [];
        foreach ($this->ddService->getDeptList() as $dept) {
            $deptIds[] = $dept['id'];
            DeptModel::updateOrCreate(['dept_id' => $dept['id']], [
                'dept_id' => $dept['id'],
                'dept_name' => $dept['name'],
                'parent_id' => $dept['parentid'] ?? 0
            ]);
        }
        DeptModel::whereNotIn('dept_id', $deptIds)->delete();
        $node = DeptModel::all(['dept_id', 'parent_id'])->toArray();
        $this->buildStructureTree($node);
        echo 'success', PHP_EOL;
    }

    /**
     * 重建索引
     * @param $data
     * @param int $pid
     * @param int $l_num
     * @param int $lv
     * @return int|mixed
     */
    function buildStructureTree(&$data, $pid = 0, $l_num = 1, $lv = 1)
    {
        foreach ($data as $_k => $node) {
            if ($pid != $node['parent_id']) continue;
            unset($data[$_k]);
            $node['l_num'] = $l_num;
            $node['r_num'] = $this->buildStructureTree($data, $node['dept_id'], $l_num + 1, $lv + 1);
            $l_num = $node['r_num'] + 1;
            DeptModel::where('dept_id', $node['dept_id'])->update([
                'l_num' => $node['l_num'],
                'r_num' => $node['r_num'],
                'lv' => $lv
            ]);
        }
        return $l_num;
    }
}
