<?php

namespace Tests\Feature;

use App\Models\Favorito;
use App\Models\Propiedad;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FavoritosTest extends TestCase
{
    private Usuario $cliente;
    private Propiedad $propiedad;

    protected function setUp(): void
    {
        parent::setUp();

        // Usar PostgreSQL en lugar de SQLite :memory:
        $this->app['config']->set('database.default', 'pgsql');
        $this->app['config']->set('database.connections.pgsql.database', 'LorentDB');

        // Reiniciar secuencia de favoritos y limpiar datos previos
        Favorito::truncate();

        $this->cliente = Usuario::create([
            'nombre'     => 'Cliente Test',
            'correo'     => 'cliente' . uniqid() . '@test.com',
            'usuario'    => 'clientetest' . uniqid(),
            'contrasena' => 'Password1',
            'rol'        => 'cliente',
        ]);

        $this->propiedad = Propiedad::create([
            'titulo'      => 'Casa de prueba Favoritos',
            'tipo'        => 'Venta',
            'zona'        => 'Zona Test',
            'precio'      => 100000,
            'area'        => 100,
            'descripcion' => 'Propiedad de prueba para test de favoritos.',
            'estado'      => 'Disponible',
        ]);
    }

    public function test_cliente_puede_agregar_favorito(): void
    {
        $this->actingAs($this->cliente);

        $response = $this->post(route('cliente.favoritos.toggle', $this->propiedad->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('favoritos', [
            'cliente_id'   => $this->cliente->id,
            'propiedad_id' => $this->propiedad->id,
        ]);
    }

    public function test_favorito_se_guarda_en_base_de_datos(): void
    {
        $this->actingAs($this->cliente);

        $this->post(route('cliente.favoritos.toggle', $this->propiedad->id));

        $this->assertDatabaseCount('favoritos', 1);
        $this->assertDatabaseHas('favoritos', [
            'cliente_id'   => $this->cliente->id,
            'propiedad_id' => $this->propiedad->id,
        ]);
    }

    public function test_vista_muestra_propiedad_favorita(): void
    {
        $this->actingAs($this->cliente);

        Favorito::create([
            'cliente_id'   => $this->cliente->id,
            'propiedad_id' => $this->propiedad->id,
        ]);

        $response = $this->get(route('cliente.favoritos.index'));

        $response->assertStatus(200);
        $response->assertSee($this->propiedad->titulo);
    }

    public function test_toggle_elimina_favorito_existente(): void
    {
        $this->actingAs($this->cliente);

        Favorito::create([
            'cliente_id'   => $this->cliente->id,
            'propiedad_id' => $this->propiedad->id,
        ]);

        $this->assertDatabaseCount('favoritos', 1);

        $response = $this->post(route('cliente.favoritos.toggle', $this->propiedad->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('favoritos', [
            'cliente_id'   => $this->cliente->id,
            'propiedad_id' => $this->propiedad->id,
        ]);
    }

    public function test_delete_elimina_favorito(): void
    {
        $this->actingAs($this->cliente);

        Favorito::create([
            'cliente_id'   => $this->cliente->id,
            'propiedad_id' => $this->propiedad->id,
        ]);

        $response = $this->delete(route('cliente.favoritos.destroy', $this->propiedad->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('favoritos', [
            'cliente_id'   => $this->cliente->id,
            'propiedad_id' => $this->propiedad->id,
        ]);
    }

    public function test_un_favorito_marca_solamente_una_tarjeta(): void
    {
        $this->actingAs($this->cliente);

        $otraPropiedad = Propiedad::create([
            'titulo'      => 'Otra casa test',
            'tipo'        => 'Alquiler',
            'zona'        => 'Zona Test 2',
            'precio'      => 50000,
            'area'        => 80,
            'descripcion' => 'Otra propiedad de prueba.',
            'estado'      => 'Disponible',
        ]);

        Favorito::create([
            'cliente_id'   => $this->cliente->id,
            'propiedad_id' => $this->propiedad->id,
        ]);

        $this->assertDatabaseHas('favoritos', [
            'cliente_id'   => $this->cliente->id,
            'propiedad_id' => $this->propiedad->id,
        ]);

        $this->assertDatabaseMissing('favoritos', [
            'cliente_id'   => $this->cliente->id,
            'propiedad_id' => $otraPropiedad->id,
        ]);
    }

    public function test_no_se_generan_duplicados(): void
    {
        $this->actingAs($this->cliente);

        $this->post(route('cliente.favoritos.toggle', $this->propiedad->id));
        $this->post(route('cliente.favoritos.toggle', $this->propiedad->id));
        $this->post(route('cliente.favoritos.toggle', $this->propiedad->id));

        $count = Favorito::where('cliente_id', $this->cliente->id)
            ->where('propiedad_id', $this->propiedad->id)
            ->count();

        $this->assertEquals(1, $count);
    }
}
