<?php

namespace App\Console\Commands;

use App\Models\WhatsAppGroup;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class WhatsAppGroupDailyQuote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp-groups:send-daily-quotes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('WhatsAppGroupDailyQuote Command Executed');
        logger()->info('WhatsAppGroupDailyQuote Command Executed');

        $groups = WhatsAppGroup::get();
        foreach ($groups as $group) {
            // Logic to send daily quote to each WhatsApp group
            $this->info("Sending daily quote to group: {$group->name}");
            logger()->info("Sending daily quote to group: {$group->name}");
            // Implement the actual sending logic here

            if ($group->dailyQuotes()->exists()) {
                $quote = $group->dailyQuotes()->latest()->first();
                WhatsAppService::sendGroupMessage($group->group_id, $quote->getFormattedQuoteAttribute());
            }
        }
    }
}
