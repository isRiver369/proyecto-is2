
class PagoService:
    def __init__(self, db_mock=None, notificador_mock=None):
        self.db = db_mock or self._crear_db_mock()
        self.notificador = notificador_mock or self._crear_notificador_mock()

    def _crear_db_mock(self):
        return {
            "reservas": {
                101: {"reserva_id": 101, "usuario_id": 501, "servicio_id": 1, "estado": "pendiente"},
                102: {"reserva_id": 102, "usuario_id": 502, "servicio_id": 2, "estado": "pendiente"},
            },
            "pagos": [],
            "usuarios": {
                501: {"nombre": "Ana", "email": "ana@example.com"},
                502: {"nombre": "Luis", "email": "luis@example.com"},
            },
            "servicios": {
                1: {"nombre_servicio": "Masaje", "precio": 50.0},
                2: {"nombre_servicio": "Corte", "precio": 20.0},
            }
        }

    def _crear_notificador_mock(self):
        return NotificadorMock()

    def registrar_pago(self, reserva_id, monto, metodo):
        try:
            if reserva_id not in self.db["reservas"]:
                raise ValueError("Reserva no encontrada")

            reserva = self.db["reservas"][reserva_id]
            usuario_id = reserva["usuario_id"]

            nuevo_pago = {
                "pago_id": len(self.db["pagos"]) + 1,
                "reserva_id": reserva_id,
                "monto": monto,
                "metodo": metodo,
                "estado": "aprobado"
            }
            self.db["pagos"].append(nuevo_pago)

            self.db["reservas"][reserva_id]["estado"] = "pagada"

            self._notificar_usuario(reserva_id, monto, usuario_id)

            return {"success": True}

        except Exception as e:
            return {"success": False, "message": str(e)}

    def _notificar_usuario(self, reserva_id, monto, usuario_id):
        if usuario_id in self.db["usuarios"]:
            usuario = self.db["usuarios"][usuario_id]
            mensaje = f"Hola {usuario['nombre']}, tu pago de ${monto} para la reserva #{reserva_id} ha sido confirmado."
            self.notificador.enviar_email(usuario["email"], "Pago Exitoso", mensaje)

    def obtener_comprobante(self, reserva_id, usuario_id):
        if reserva_id not in self.db["reservas"]:
            return None
        reserva = self.db["reservas"][reserva_id]
        if reserva["usuario_id"] != usuario_id:
            return None

        servicio = self.db["servicios"][reserva["servicio_id"]]
        usuario = self.db["usuarios"][usuario_id]
        pago = next((p for p in self.db["pagos"] if p["reserva_id"] == reserva_id), None)

        if not pago:
            return None

        return {
            "pago_id": pago["pago_id"],
            "monto": pago["monto"],
            "metodo_pago": pago["metodo"],
            "fecha_reserva": "2025-12-14",
            "reserva_id": reserva_id,
            "nombre_servicio": servicio["nombre_servicio"],
            "nombre": usuario["nombre"],
            "apellido": "",
            "email": usuario["email"]
        }


class NotificadorMock:
    def __init__(self):
        self.enviados = []

    def enviar_email(self, email, asunto, mensaje):
        self.enviados.append({"email": email, "asunto": asunto, "mensaje": mensaje})

#actualizando prueba
