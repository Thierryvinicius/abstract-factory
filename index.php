<?php

// ===============================
// Interfaces dos Produtos
// ===============================

/**
 * Interface para o produto Cadeira.
 * Define o que toda cadeira deve saber fazer.
 */
interface Cadeira {
    public function sentar();
}

/**
 * Interface para o produto Mesa.
 * Define o que toda mesa deve saber fazer.
 */
interface Mesa {
    public function colocarObjeto();
}

// ===============================
// Produtos Concretos - Estilo Moderno
// ===============================

class CadeiraModerna implements Cadeira {
    public function sentar() {
        echo "🪑 Sentando em uma cadeira moderna.\n";
    }
}

class MesaModerna implements Mesa {
    public function colocarObjeto() {
        echo "🟫 Colocando objeto sobre uma mesa moderna.\n";
    }
}

// ===============================
// Produtos Concretos - Estilo Vitoriano
// ===============================

class CadeiraVitoriana implements Cadeira {
    public function sentar() {
        echo "🪑 Sentando em uma cadeira vitoriana elegante.\n";
    }
}

class MesaVitoriana implements Mesa {
    public function colocarObjeto() {
        echo "🟫 Colocando objeto sobre uma mesa vitoriana decorada.\n";
    }
}

// ===============================
// Fábrica Abstrata
// ===============================

/**
 * Interface para a fábrica de móveis.
 * Define métodos para criar todos os tipos de móveis (produtos).
 */
interface FabricaDeMobilia {
    public function criarCadeira(): Cadeira;
    public function criarMesa(): Mesa;
}

// ===============================
// Fábricas Concretas
// ===============================

/**
 * Fábrica que cria móveis no estilo moderno.
 */
class FabricaModerna implements FabricaDeMobilia {
    public function criarCadeira(): Cadeira {
        return new CadeiraModerna();
    }

    public function criarMesa(): Mesa {
        return new MesaModerna();
    }
}

/**
 * Fábrica que cria móveis no estilo vitoriano.
 */
class FabricaVitoriana implements FabricaDeMobilia {
    public function criarCadeira(): Cadeira {
        return new CadeiraVitoriana();
    }

    public function criarMesa(): Mesa {
        return new MesaVitoriana();
    }
}

// ===============================
// Código Cliente
// ===============================

/**
 * O cliente usa a fábrica abstrata, sem saber a implementação concreta.
 */
function montarSala(FabricaDeMobilia $fabrica) {
    echo "🛋️ Montando uma sala com a fábrica escolhida:\n";

    $cadeira = $fabrica->criarCadeira();
    $mesa = $fabrica->criarMesa();

    $cadeira->sentar();
    $mesa->colocarObjeto();
}

// ===============================
// Testando
// ===============================

echo "=== Estilo Moderno ===\n";
montarSala(new FabricaModerna());

echo "\n=== Estilo Vitoriano ===\n";
montarSala(new FabricaVitoriana());
