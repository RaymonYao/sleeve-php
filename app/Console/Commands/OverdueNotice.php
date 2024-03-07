<?php


namespace App\Console\Commands;


use App\Models\TicketModel;
use App\Services\MsgService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Expression;

class OverdueNotice extends Command
{
    protected $signature = 'command:overdueNotice';

    protected $description = '逾期通知';

    /**
     * @var MsgService
     */
    protected $msgService;

    public function __construct(MsgService $msgService)
    {
        parent::__construct();
        $this->msgService = $msgService;
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        $ticketObjs = TicketModel::select(['ticket_id', new Expression('DATEDIFF(expect_finish_at,CURRENT_DATE()) as overdue_type')])
            ->whereIn('status', [TicketModel::T_NEW, TicketModel::T_WIP])
            ->whereRaw('DATEDIFF(expect_finish_at,CURRENT_DATE()) <= ' . TicketModel::ABOUT_TO_OVERDUE_DAYS)
            ->get();
        foreach ($ticketObjs as $ticketObj) {
            $this->msgService->sendMsg($ticketObj->ticket_id, $ticketObj['overdue_type'] >= 0 ? MsgService::MSG_ABOUT_TO_OVERDUE : MsgService::MSG_OVERDUE);
        }
        echo 'success', PHP_EOL;
    }
}
