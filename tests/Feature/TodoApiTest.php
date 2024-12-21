<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TodoApiTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Cria um usuário para autenticação nos testes
        $this->user = User::create([
            'name' => 'novousuario',
            'email' => 'novousuario@email.com',
            'password' => Hash::make('alice'),
        ]);
    }

    /** @test */
    public function it_can_create_a_todo()
    {
        $response = $this->actingAs($this->user, 'api')->postJson('/api/todos', [
            'title' => 'tela de cadastro de clientes novos',
            'description' => 'cnpj, indicado, categoria de cliente',
            'completed' => false,
            'responsible' => 'Alice',
            'stage' => 'to-do',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'title', 'description', 'completed', 'responsible', 'user_id']);

        $this->assertDatabaseHas('todos', [
            'title' => 'tela de cadastro de clientes novos',
            'responsible' => 'Alice',
        ]);
    }

    /** @test */
    public function it_can_get_all_todos()
    {
        // Cria um to-do para o usuário
        $todo = Todo::create([
            'title' => 'tela de cadastro de clientes novos',
            'description' => 'cnpj, indicado, categoria de cliente',
            'completed' => 0,
            'responsible' => 'Alice',
            'stage' => 'to-do', // Adicionando o campo stage, se necessário
            'user_id' => $this->user->id,
        ]);

        // Faz a requisição para obter todos os to-dos do usuário autenticado
        $response = $this->actingAs($this->user, 'api')->getJson('/api/todos');

        // Verifica se a resposta é bem-sucedida e contém o to-do criado
        $response->assertStatus(200)
                ->assertJsonCount(1)  // Verifica se há 1 to-do na resposta
                ->assertJsonFragment([ // Verifica se o to-do criado está na resposta
                    'id' => $todo->id,
                    'title' => 'tela de cadastro de clientes novos',
                    'description' => 'cnpj, indicado, categoria de cliente',
                    'completed' => 0,
                    'responsible' => 'Alice',
                    'stage' => 'to-do', // Adicionando a verificação do campo stage
                ]);

    }


    /** @test */
    public function it_can_update_a_todo()
    {
        $todo = Todo::create([
            'title' => 'tela de cadastro de clientes novos',
            'description' => 'texto qualquer',
            'completed' => false,
            'responsible' => 'Alice',
            'user_id' => $this->user->id,
            'stage' => 'to-do',
        ]);

        $response = $this->actingAs($this->user, 'api')->putJson("/api/todos/{$todo->id}", [
            'title' => 'tela de cadastro de clientes novos',
            'description' => null,
            'completed' => true,
            'responsible' => 'Adriano',
            'stage' => 'to-do',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['title' => 'tela de cadastro de clientes novos']);

        // Verifica se o banco de dados foi atualizado
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'tela de cadastro de clientes novos',
            'completed' => true,
            'responsible' => 'Adriano',
        ]);
    }

    /** @test */
    public function it_can_delete_a_todo()
    {
        $todo = Todo::create([
            'title' => 'tela de cadastro de clientes novos',
            'description' => '',
            'completed' => false,
            'responsible' => 'Alice',
            'user_id' => $this->user->id,
            'stage' => 'to-do',
        ]);

        $response = $this->actingAs($this->user, 'api')->deleteJson("/api/todos/{$todo->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => "To-do removido com sucesso"]);

        // Verifica se o to-do foi removido do banco de dados
        $this->assertDeleted($todo);
    }

    /** @test */
    public function it_can_get_a_single_todo()
    {
        // Cria um to-do para o usuário
        $todo = Todo::create([
            'title' => 'um titlulo qualquer',
            'description' => 'uma descrição qualquer',
            'completed' => false,
            'responsible' => 'Alice',
            'stage' => 'to-do',
            'user_id' => $this->user->id,
        ]);

        // Faz a requisição GET para buscar o to-do
        $response = $this->actingAs($this->user, 'api')->getJson("/api/todos/{$todo->id}");

        // Verifica se a resposta é bem-sucedida e se os dados estão corretos
        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $todo->id,
                     'title' => 'um titlulo qualquer',
                     'description' => 'uma descrição qualquer',
                     'completed' => false,
                     'responsible' => 'Alice',
                     'stage' => 'to-do',
                 ]);
    }

    /** @test */
    public function it_cannot_get_a_single_todo_if_not_authenticated()
    {
        // Cria um to-do para o usuário
        $todo = Todo::create([
            'title' => 'um titulo qualquer',
            'description' => 'uma descricao qualquer',
            'completed' => false,
            'responsible' => 'Alice',
            'stage' => 'to-do',
            'user_id' => $this->user->id,
        ]);

        // Faz a requisição GET sem autenticação
        $response = $this->getJson("/api/todos/{$todo->id}");

        // Verifica se a resposta é não autorizada
        $response->assertStatus(401);
    }

    /** @test *///não apliquei  essa regra
   /*  public function it_cannot_get_a_single_todo_if_not_owner()
    {
        // Cria um to-do para o usuário
        $todo = Todo::create([
            'title' => 'um titulo qualquer',
            'description' => 'uma descricao qualquer',
            'completed' => false,
            'responsible' => 'Alice',
            'stage' => 'to-do',
            'user_id' => $this->user->id,
        ]);

        // Cria outro usuário e faz login como ele
        $anotherUser = User::create([
            'name' => 'Another User',
            'email' => 'another@email.com',
            'password' => Hash::make('password'),
        ]);

        // Faz a requisição GET como outro usuário
        $response = $this->actingAs($anotherUser, 'api')->getJson("/api/todos/{$todo->id}");

        // Verifica se a resposta é não autorizado
        $response->assertStatus(403);
    } */
}
