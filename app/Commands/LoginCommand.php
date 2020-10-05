<?php

namespace App\Commands;

use App\Commands\Concerns\EnsureHasToken;
use App\Support\TokenNodeVisitor;
use LaravelZero\Framework\Commands\Command;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\Parser\Php7;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpClient\HttpClient;

class LoginCommand extends Command
{
    const LOGIN_URL = 'https://forge.laravel.com/auth/login';

    const TOKEN_URL = 'https://forge.laravel.com/oauth/personal-access-tokens';

    use EnsureHasToken;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'login {--force}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Login to Laravel Forge';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->hasToken() && ! $this->option('force')) {
            $this->warn('You are already logged in');
            return 0;
        }

        $email = $this->ask('What is your Laravel Forge email?');
        $password = $this->secret('What is your password?');

        $this->info('Logging in...');

        $this->login($email, $password);
    }

    protected function login($email, $password)
    {
        $browser = new HttpBrowser(HttpClient::create());

        $browser->request('GET', static::LOGIN_URL);

        $browser->submitForm('Sign In', [
            'email' => $email,
            'password' => $password
        ]);

        $uri = $browser->getHistory()->current()->getUri();

        if ($uri === static::LOGIN_URL) {
            $this->error('Invalid credentials.');
            exit();
        }

        $browser->request('POST', static::TOKEN_URL, [
            'name' => 'Forge-CLI',
            'scopes' => [],
        ]);

        /** @var Response $response */
        $response = $browser->getResponse();

        if ($response->getStatusCode() !== 200) {
            $this->error('Unable to create API Token');
            $this->error($response->getContent());
            exit();
        }

        $responseObject = json_decode($response->getContent());
        $this->saveToken($responseObject->accessToken);

        $this->info('Retrieved and stored your Forge access token!');
        $this->info('You\'re all set and ready to go.');
    }

    protected function saveToken($token)
    {
        $configFile = implode(DIRECTORY_SEPARATOR, [
            $_SERVER['HOME'] ?? $_SERVER['USERPROFILE'],
            '.forge',
            'config.php',
        ]);

        if (! file_exists($configFile)) {
            @mkdir(dirname($configFile), 0777, true);
            $updatedConfigFile = $this->modifyConfigurationFile(base_path('config/forge.php'), $token);
        } else {
            $updatedConfigFile = $this->modifyConfigurationFile($configFile, $token);
        }

        file_put_contents($configFile, $updatedConfigFile);

        return;
    }

    protected function modifyConfigurationFile(string $configFile, string $token)
    {
        $lexer = new Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);
        $parser = new Php7($lexer);

        $oldStmts = $parser->parse(file_get_contents($configFile));
        $oldTokens = $lexer->getTokens();

        $nodeTraverser = new NodeTraverser;
        $nodeTraverser->addVisitor(new CloningVisitor());
        $newStmts = $nodeTraverser->traverse($oldStmts);

        $nodeTraverser = new NodeTraverser;
        $nodeTraverser->addVisitor(new TokenNodeVisitor($token));

        $newStmts = $nodeTraverser->traverse($newStmts);

        $prettyPrinter = new Standard();

        return $prettyPrinter->printFormatPreserving($newStmts, $oldStmts, $oldTokens);
    }
}
