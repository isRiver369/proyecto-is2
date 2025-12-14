import unittest

# --- Clase a probar (Simulación de ReservaService.php) ---
class ValidadorEstado:
    def se_puede_cancelar(self, estado_actual):
        estados_permitidos = ['pendiente', 'confirmada']
        return estado_actual in estados_permitidos

    def se_puede_pagar(self, estado_actual):
        return estado_actual == 'pendiente'

# --- Prueba Unitaria ---
class TestValidador(unittest.TestCase):
    def test_reglas_cancelacion(self):
        print(">>> Ejecutando Test Aislado: Validador de Estados")
        validador = ValidadorEstado()
        self.assertTrue(validador.se_puede_cancelar('pendiente'))
        self.assertFalse(validador.se_puede_cancelar('pagada'))

if __name__ == '__main__':
    unittest.main()
