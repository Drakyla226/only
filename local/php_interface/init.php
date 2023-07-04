<?php
\CAgent::AddAgent(
    '\Dev\Site\Agents\IBlock::clearOldLogs();',
    'dev.site',
    'N',
    3600,
    '',
    'Y',
    date('d.m.Y H:00:00', strtotime('+1 hour')),
);