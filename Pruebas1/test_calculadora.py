import unittest

# --- Case a probar (Simulación de ver_comprobante.php) ---
class CalculadoraFinanciera:
    def desglosar_impuestos(self, total, tasa_impuesto):
        if total < 0 or tasa_impuesto < 0:
            return None 
        factor_divisor = 1 + (tasa_impuesto / 100)
        subtotal = total / factor_divisor
        iva = total - subtotal
        return {
            'subtotal': round(subtotal, 2),
            'iva': round(iva, 2),
            'total': round(total, 2)
        }

# --- Prueba Unitaria ---
class TestCalculadora(unittest.TestCase):
    def test_calculo_iva_15_porciento(self):
        print(">>> Ejecutando Test Aislado: Calculadora Financiera")
        calc = CalculadoraFinanciera()
        resultado = calc.desglosar_impuestos(115.00, 15.00)
        self.assertEqual(resultado['subtotal'], 100.00)
        self.assertEqual(resultado['iva'], 15.00)

if __name__ == '__main__':
    unittest.main()
