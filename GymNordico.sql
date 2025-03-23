/*Tablas y secuencias*/

CREATE TABLE empleados (
    id NUMBER PRIMARY KEY,
    username VARCHAR2(255) NOT NULL, 
    password VARCHAR2(255) NOT NULL,
    email VARCHAR2(255) NOT NULL  
     
);

select * from empleados;

CREATE SEQUENCE empleados_seq
START WITH 1
INCREMENT BY 1;

create table clientes (
    cedula VARCHAR2(20) PRIMARY KEY,
    nombre varchar2(50) not null,
    apellido varchar2(50) not null,
    correo varchar2(100) unique not null,
    telefono varchar2(15),
    direccion varchar2(255),
    fecha_registro date
);

select * from clientes;

CREATE TABLE miembros (
    id_miembro NUMBER PRIMARY KEY,
    cedula VARCHAR2(20) NOT NULL,
    activo CHAR(1) DEFAULT 'y',
    costo_mensual NUMBER(10, 2) NOT NULL,
    CONSTRAINT fk_miembro_clientes FOREIGN KEY (cedula) REFERENCES clientes(cedula)
);

CREATE SEQUENCE miembros_seq
START WITH 1
INCREMENT BY 1;

CREATE TABLE instructores (
    id_instructor INT PRIMARY KEY,
    nombre VARCHAR2(100),
    especialidad VARCHAR2(100),
    telefono VARCHAR2(20),
    correo VARCHAR2(100),
    salario DECIMAL(10,2)
);

CREATE SEQUENCE instructores_seq
START WITH 1
INCREMENT BY 1;

CREATE TABLE clases (
    id_clase INT PRIMARY KEY,
    nombre_clase VARCHAR2(100),
    descripcion VARCHAR2(255),
    id_instructor INT,
    CONSTRAINT fk_clase_instructor FOREIGN KEY (id_instructor) REFERENCES instructores(id_instructor)
);

CREATE SEQUENCE clases_seq
START WITH 1
INCREMENT BY 1;

CREATE TABLE sucursales (
    id_gimnasio INT PRIMARY KEY,
    nombre_sucursal VARCHAR2(100),
    direccion VARCHAR2(255),
    telefono VARCHAR2(20),
    ciudad VARCHAR2(100)
);

CREATE TABLE equipos_gimnasio (
    id_equipo INT PRIMARY KEY,
    nombre VARCHAR2(100),
    tipo VARCHAR2(100),
    estado VARCHAR2(50),
    fecha_compra DATE,
    id_gimnasio INT,
    CONSTRAINT fk_equipo_gimnasio FOREIGN KEY (id_gimnasio) REFERENCES sucursales(id_gimnasio)
);

CREATE SEQUENCE equipos_gimnasio_seq
START WITH 1
INCREMENT BY 1;

CREATE TABLE mantenimiento_equipos (
    id_mantenimiento INT PRIMARY KEY,
    id_equipo INT,
    fecha_mantenimiento DATE,
    descripcion VARCHAR2(255),
    estado VARCHAR2(50),
    CONSTRAINT fk_mantenimiento_equipo FOREIGN KEY (id_equipo) REFERENCES equipos_gimnasio(id_equipo)
);

CREATE SEQUENCE mantenimiento_equipos_seq
START WITH 1
INCREMENT BY 1;

CREATE TABLE productos_tienda (
    id_producto INT PRIMARY KEY,
    nombre_producto VARCHAR2(100),
    precio DECIMAL(10,2),
    stock INT,
    tipo_producto VARCHAR2(100)
);

CREATE SEQUENCE productos_tienda_seq
START WITH 1
INCREMENT BY 1;

CREATE TABLE ventas_tienda (
    id_venta INT PRIMARY KEY,
    cedula VARCHAR2(20),
    id_producto INT,
    cantidad INT,
    total DECIMAL(10,2),
    fecha_venta DATE,
    CONSTRAINT fk_venta_producto FOREIGN KEY (id_producto) REFERENCES productos_tienda(id_producto),
    CONSTRAINT fk_venta_cliente FOREIGN KEY (cedula) REFERENCES clientes(cedula)
);

CREATE SEQUENCE ventas_tienda_seq
START WITH 1
INCREMENT BY 1;

CREATE TABLE pagos (
    id_pago INT PRIMARY KEY,
    id_miembro INT,
    fecha_pago DATE,
    monto DECIMAL(10,2),
    metodo_pago VARCHAR2(50),
    CONSTRAINT fk_pago_membresia FOREIGN KEY (id_miembro) REFERENCES miembros(id_miembro)
);

CREATE SEQUENCE pagos_seq
START WITH 1
INCREMENT BY 1;

/*vistas*/

CREATE OR REPLACE VIEW cliente_datos_personales AS
SELECT cedula, nombre, apellido
FROM clientes;

CREATE OR REPLACE VIEW cliente_datos_contacto AS
SELECT cedula, correo, telefono, direccion
FROM clientes;

CREATE OR REPLACE VIEW cliente_estado_membresia AS
SELECT cedula, activo
FROM miembros;

CREATE OR REPLACE VIEW instructor_especialidad AS
SELECT nombre, especialidad
FROM instructores;

CREATE OR REPLACE VIEW instructor_contacto AS
SELECT nombre, telefono, correo
FROM instructores;

CREATE OR REPLACE VIEW clase_instructor AS
SELECT nombre_clase, id_instructor
FROM clases;

CREATE OR REPLACE VIEW equipos_estado AS
SELECT nombre, estado
FROM equipos_gimnasio;

CREATE OR REPLACE VIEW producto_precio_stock AS
SELECT id_producto, nombre_producto AS nombre, precio, stock
FROM productos_tienda;

CREATE OR REPLACE VIEW consulta_metodoPago AS
SELECT id_pago, metodo_pago
FROM pagos;

CREATE OR REPLACE VIEW telefono_gimnasio AS
SELECT id_gimnasio, nombre_sucursal, telefono
FROM sucursales;


/*SP*/


CREATE OR REPLACE PROCEDURE insertar_cliente (
    p_cedula IN VARCHAR2,
    p_nombre IN VARCHAR2,
    p_apellido IN VARCHAR2,
    p_correo IN VARCHAR2,
    p_telefono IN VARCHAR2,
    p_direccion IN VARCHAR2,
    p_fecha_registro IN DATE
)
IS
BEGIN
    INSERT INTO clientes (cedula, nombre, apellido, correo, telefono, direccion, fecha_registro)
    VALUES (p_cedula, p_nombre, p_apellido, p_correo, p_telefono, p_direccion, p_fecha_registro);
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE;
END insertar_cliente;




















