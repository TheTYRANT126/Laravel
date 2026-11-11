-- Las tablas ya existen en la exportación proyecto_ruben.sql.
-- Ejecuta este script en la base de datos proyecto_ruben si faltan.
-- IF NOT EXISTS conserva las tablas y datos existentes.
CREATE TABLE IF NOT EXISTS venta (
  id_venta INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fecha_venta DATETIME NOT NULL,
  id_cliente INT UNSIGNED NOT NULL,
  FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS det_venta (
  id_detalle INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_producto INT UNSIGNED NOT NULL,
  id_venta INT UNSIGNED NOT NULL,
  cantidad INT UNSIGNED NOT NULL,
  precio_h DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (id_producto) REFERENCES producto(id_producto),
  FOREIGN KEY (id_venta) REFERENCES venta(id_venta)
) ENGINE=InnoDB;

-- Ejecuta esta parte una sola vez si venta todavía no tiene la columna total.
ALTER TABLE venta ADD COLUMN total DECIMAL(20,2) NOT NULL DEFAULT 0;
UPDATE venta SET total = COALESCE(
  (SELECT SUM(cantidad * precio_h) FROM det_venta WHERE det_venta.id_venta = venta.id_venta), 0
);
