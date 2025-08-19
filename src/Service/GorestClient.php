<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GorestClient
{
    private HttpClientInterface $client;
    private string $token;

    public function __construct(HttpClientInterface $client, string $gorestToken)
    {
        $this->client = $client;
        $this->token = $gorestToken;
    }

    public function fetchUsers(string $query = '', int $page = 1): array
    {
        $url = 'https://gorest.co.in/public/v2/users?page=' . $page;
    
        if ($query) {
            $url .= '&name=' . urlencode($query);
        }
    
        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ]
        ]);
    
        $users = $response->toArray();
    
        // Sortowanie malejąco po ID
        usort($users, fn($a, $b) => $b['id'] <=> $a['id']);
    
        $page = (int) $response->getHeaders()['x-pagination-page'][0] ?? 1;
        $pages = (int) $response->getHeaders()['x-pagination-pages'][0] ?? 1;
        
        $hasPrev = $page > 1;
        $hasNext = $page < $pages;        
    
        return [$users, $hasNext, $hasPrev];
    }
    
    

    public function updateUser(int $id, array $data): array
    {
        $url = 'https://gorest.co.in/public/v2/users/' . $id;

        $response = $this->client->request('PATCH', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);        

        return $response->toArray(false);
    }
}
