<?php
\CAgent::AddAgent(
    '\Dev\Site\IBlock::clearOldLogs();',
    'dev.site',
    'N',
    3600,
    '',
    'Y',
    date('d.m.Y H:i:s'),
);