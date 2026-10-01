<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class DeployController extends Controller
{
    public function __invoke(Request $request)
    {
        $given = (string) $request->query('key', '');
        $expected = $this->token();

        if ($expected === '' || ! hash_equals($expected, $given)) {
            abort(404);
        }

        @set_time_limit(300);

        $lines = [];
        $lines[] = $this->run('config:clear');
        $lines[] = $this->run('cache:clear');
        $lines[] = $this->run('view:clear');
        $lines[] = $this->run('route:clear');
        $lines[] = $this->run('migrate', ['--force' => true]);
        $lines[] = $this->run('db:seed', ['--force' => true, '--class' => 'Database\\Seeders\\DatabaseSeeder']);
        $lines[] = $this->linkStorage();

        $body = e(implode("\n\n", $lines));

        return response(
            '<!DOCTYPE html><html><head><meta charset="utf-8"><title>DHC Shoes POS deploy</title></head><body style="font-family:sans-serif;padding:24px"><h1>DHC Shoes POS deploy finished</h1><pre style="white-space:pre-wrap">'.$body.'</pre></body></html>',
            200,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    private function run(string $command, array $parameters = []): string
    {
        try {
            $code = Artisan::call($command, $parameters);
            $output = trim(Artisan::output());

            return '$ php artisan '.$command.($parameters ? ' '.json_encode($parameters) : '')."\nexit {$code}\n".($output !== '' ? $output : 'ok');
        } catch (Throwable $e) {
            return '$ php artisan '.$command."\nFAILED\n".$e->getMessage();
        }
    }

    private function linkStorage(): string
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        if (! is_dir($target)) {
            mkdir($target, 0755, true);
        }

        if (is_link($link) || file_exists($link)) {
            return "storage link already exists\n{$link}";
        }

        try {
            Artisan::call('storage:link');
            $output = trim(Artisan::output());

            return "storage:link\n".($output !== '' ? $output : 'ok');
        } catch (Throwable $e) {
            if (@symlink($target, $link)) {
                return "storage link created\n{$link} -> {$target}";
            }

            return "storage:link FAILED\n".$e->getMessage()."\nCreate this symlink in cPanel File Manager:\n{$link} -> {$target}";
        }
    }

    private function token(): string
    {
        $token = (string) config('deploy.token');
        if ($token !== '') {
            return $token;
        }

        $env = @file_get_contents(base_path('.env'));
        if (! is_string($env) || ! preg_match('/^DEPLOY_TOKEN=(.*)$/m', $env, $match)) {
            return '';
        }

        return trim($match[1], " \t\"'");
    }
}
