<?php

echo get_class($exception), "\n";
echo 'Message: ', $exception->getMessage(), "\n\n";
echo '## ', $exception->getFile(), '(', $exception->getLine(), ')', "\n";
echo $exception->getTraceAsString();
