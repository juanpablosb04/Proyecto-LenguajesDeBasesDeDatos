/*Tablas y secuencias*/

CREATE TABLE empleados (
    id NUMBER PRIMARY KEY,
    username VARCHAR2(255) NOT NULL, 
    password VARCHAR2(255) NOT NULL,
    email VARCHAR2(255) NOT NULL  
     
);

CREATE SEQUENCE empleados_seq
START WITH 1
INCREMENT BY 1
NOCACHE;

create table clientes (
    cedula VARCHAR2(20) PRIMARY KEY,
    nombre varchar2(50) not null,
    apellido varchar2(50) not null,
    correo varchar2(100) unique not null,
    telefono varchar2(15),
    direccion varchar2(255),
    fecha_registro date
);

CREATE TABLE miembros (
    id_miembro NUMBER PRIMARY KEY,
    cedula VARCHAR2(20) NOT NULL,
    activo CHAR(1) DEFAULT 'y',
    costo_mensual NUMBER(10, 2) NOT NULL,
    CONSTRAINT fk_miembro_clientes FOREIGN KEY (cedula) REFERENCES clientes(cedula)
);

CREATE SEQUENCE miembros_seq
START WITH 1
INCREMENT BY 1
NOCACHE;

CREATE TABLE instructores (
    id_instructor INT PRIMARY KEY,
    nombre VARCHAR2(100),
    especialidad VARCHAR2(100),
    telefono VARCHAR2(20),
    correo VARCHAR2(100),
    salario NUMBER(10, 2) NOT NULL
);

CREATE SEQUENCE instructores_seq
START WITH 1
INCREMENT BY 1
NOCACHE;

CREATE TABLE clases (
    id_clase INT PRIMARY KEY,
    nombre_clase VARCHAR2(100),
    descripcion VARCHAR2(255),
    id_instructor INT,
    CONSTRAINT fk_clase_instructor FOREIGN KEY (id_instructor) REFERENCES instructores(id_instructor)
);

CREATE SEQUENCE clases_seq
START WITH 1
INCREMENT BY 1
NOCACHE;

CREATE TABLE sucursales (
    id_gimnasio INT PRIMARY KEY,
    nombre_sucursal VARCHAR2(100),
    direccion VARCHAR2(255),
    telefono VARCHAR2(20),
    ciudad VARCHAR2(100)
);

CREATE SEQUENCE sucursales_seq
START WITH 1
INCREMENT BY 1;

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
INCREMENT BY 1
NOCACHE;

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
INCREMENT BY 1
NOCACHE;

CREATE TABLE productos_tienda (
    id_producto INT PRIMARY KEY,
    nombre_producto VARCHAR2(100),
    precio NUMBER(10,2),
    stock INT,
    tipo_producto VARCHAR2(100)
);

CREATE SEQUENCE productos_tienda_seq
START WITH 1
INCREMENT BY 1
NOCACHE;

CREATE TABLE ventas_tienda (
    id_venta INT PRIMARY KEY,
    cedula VARCHAR2(20),
    id_producto INT,
    cantidad INT,
    total  NUMBER(10,2),
    fecha_venta DATE,
    CONSTRAINT fk_venta_producto FOREIGN KEY (id_producto) REFERENCES productos_tienda(id_producto),
    CONSTRAINT fk_venta_cliente FOREIGN KEY (cedula) REFERENCES clientes(cedula)
);

CREATE SEQUENCE ventas_tienda_seq
START WITH 1
INCREMENT BY 1
NOCACHE;

CREATE TABLE pagos (
    id_pago INT PRIMARY KEY,
    id_miembro INT,
    fecha_pago DATE,
    monto NUMBER(10,2),
    metodo_pago VARCHAR2(50),
    CONSTRAINT fk_pago_membresia FOREIGN KEY (id_miembro) REFERENCES miembros(id_miembro)
);

CREATE SEQUENCE pagos_seq
START WITH 1
INCREMENT BY 1
NOCACHE;

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

/*SP Empleados*/

CREATE OR REPLACE PROCEDURE registrar_empleado (
    emp_username IN empleados.username%TYPE,
    emp_password IN empleados.password%TYPE,
    emp_email IN empleados.email%TYPE
) AS
BEGIN
    INSERT INTO empleados (id, username, password, email)
    VALUES (empleados_seq.NEXTVAL, emp_username, emp_password, emp_email);
    
    COMMIT;
END registrar_empleado;
/

/*SP para clientes*/

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


CREATE OR REPLACE PROCEDURE actualizar_cliente(
    p_cedula        IN clientes.cedula%TYPE,
    p_nombre        IN clientes.nombre%TYPE,
    p_apellido      IN clientes.apellido%TYPE,
    p_correo        IN clientes.correo%TYPE,
    p_telefono      IN clientes.telefono%TYPE,
    p_direccion     IN clientes.direccion%TYPE,
    p_fecha_registro IN VARCHAR2
) AS
BEGIN
    UPDATE clientes 
    SET nombre = p_nombre, 
        apellido = p_apellido, 
        correo = p_correo, 
        telefono = p_telefono, 
        direccion = p_direccion, 
        fecha_registro = TO_DATE(p_fecha_registro, 'YYYY-MM-DD')
    WHERE cedula = p_cedula;
    
    COMMIT;
END actualizar_cliente;

CREATE OR REPLACE PROCEDURE eliminar_cliente(
    c_cedula IN clientes.cedula%TYPE
) AS
BEGIN
    DELETE FROM clientes WHERE cedula = c_cedula;
    COMMIT;
END eliminar_cliente;
/


/*SP para Miembros*/

CREATE OR REPLACE PROCEDURE registrar_membresia(
    m_cedula IN VARCHAR2,
    m_costo_mensual IN NUMBER
) AS
BEGIN
    INSERT INTO miembros (id_miembro, cedula, costo_mensual)
    SELECT miembros_seq.NEXTVAL, m_cedula, m_costo_mensual
    FROM dual
    WHERE EXISTS (SELECT 1 FROM clientes WHERE cedula = m_cedula);
    
    COMMIT;
END registrar_membresia;
/

CREATE OR REPLACE PROCEDURE actualizar_membresia (
    m_cedula IN miembros.cedula%TYPE,
    m_costo_mensual IN miembros.costo_mensual%TYPE,
    m_estado IN miembros.activo%TYPE
) AS
BEGIN
    UPDATE miembros 
    SET costo_mensual = m_costo_mensual, 
        activo = m_estado
    WHERE cedula = m_cedula;
    
    COMMIT;
END actualizar_membresia;
/

CREATE OR REPLACE PROCEDURE eliminar_membresia (
    m_cedula IN miembros.cedula%TYPE
) AS
BEGIN
    DELETE FROM miembros 
    WHERE cedula = m_cedula;
    
    COMMIT;
END eliminar_membresia;
/

/*SP Instructores*/

CREATE OR REPLACE PROCEDURE registrar_instructor (
    ins_nombre IN instructores.nombre%TYPE,
    ins_especialidad IN instructores.especialidad%TYPE,
    ins_telefono IN instructores.telefono%TYPE,
    ins_correo IN instructores.correo%TYPE,
    ins_salario IN instructores.salario%TYPE
) AS
BEGIN
    INSERT INTO instructores (id_instructor, nombre, especialidad, telefono, correo, salario)
    VALUES (instructores_seq.NEXTVAL, ins_nombre, ins_especialidad, ins_telefono, ins_correo, ins_salario);
    
    COMMIT;
END registrar_instructor;
/

CREATE OR REPLACE PROCEDURE actualizar_instructor (
    ins_id IN instructores.id_instructor%TYPE,
    ins_nombre IN instructores.nombre%TYPE,
    ins_especialidad IN instructores.especialidad%TYPE,
    ins_telefono IN instructores.telefono%TYPE,
    ins_correo IN instructores.correo%TYPE,
    ins_salario IN instructores.salario%TYPE
) AS
BEGIN
    UPDATE instructores 
    SET nombre = ins_nombre, 
        especialidad = ins_especialidad, 
        telefono = ins_telefono, 
        correo = ins_correo, 
        salario = ins_salario
    WHERE id_instructor = ins_id;
    
    COMMIT;
END actualizar_instructor;
/

CREATE OR REPLACE PROCEDURE eliminar_instructor (
    ins_id_instructor IN instructores.id_instructor%TYPE
) AS
BEGIN
    DELETE FROM instructores 
    WHERE id_instructor = ins_id_instructor;
    
    COMMIT;
END eliminar_instructor;
/

/*SP CLASES*/

CREATE OR REPLACE PROCEDURE registrar_clase (
    c_nombre_clase IN clases.nombre_clase%TYPE,
    c_descripcion IN clases.descripcion%TYPE,
    c_id_instructor IN clases.id_instructor%TYPE
) AS
    existe_instructor NUMBER;
BEGIN
    SELECT COUNT(*) INTO existe_instructor
    FROM instructores
    WHERE id_instructor = c_id_instructor;
    
    IF existe_instructor = 0 THEN
        RETURN;
    END IF;
    

    INSERT INTO clases (id_clase, nombre_clase, descripcion, id_instructor)
    VALUES (clases_seq.NEXTVAL, c_nombre_clase, c_descripcion, c_id_instructor);
    
    COMMIT;
END registrar_clase;
/

CREATE OR REPLACE PROCEDURE actualizar_clase (
    c_id IN clases.id_clase%TYPE,
    c_nombre_clase IN clases.nombre_clase%TYPE,
    c_descripcion IN clases.descripcion%TYPE,
    c_id_instructor IN clases.id_instructor%TYPE
) AS
BEGIN
    UPDATE clases 
    SET nombre_clase = c_nombre_clase, 
        descripcion = c_descripcion, 
        id_instructor = c_id_instructor
    WHERE id_clase = c_id;
    
    COMMIT;
END actualizar_clase;
/

CREATE OR REPLACE PROCEDURE eliminar_clase (
    c_id_clase IN clases.id_clase%TYPE
) AS
BEGIN
    DELETE FROM clases 
    WHERE id_clase = c_id_clase;
    
    COMMIT;
END eliminar_clase;
/

/*SP sucursales*/

CREATE OR REPLACE PROCEDURE registrar_sucursal (
    s_nombre_sucursal IN sucursales.nombre_sucursal%TYPE,
    s_direccion IN sucursales.direccion%TYPE,
    s_telefono IN sucursales.telefono%TYPE,
    s_ciudad IN sucursales.ciudad%TYPE
) AS
BEGIN
    INSERT INTO sucursales (id_gimnasio, nombre_sucursal, direccion, telefono, ciudad)
    VALUES (sucursales_seq.NEXTVAL, s_nombre_sucursal, s_direccion, s_telefono, s_ciudad);
    
    COMMIT;
END registrar_sucursal;
/

CREATE OR REPLACE PROCEDURE actualizar_sucursal(
    s_id_gimnasio     IN sucursales.id_gimnasio%TYPE,
    s_nombre_sucursal IN sucursales.nombre_sucursal%TYPE,
    s_direccion       IN sucursales.direccion%TYPE,
    s_telefono        IN sucursales.telefono%TYPE,
    s_ciudad          IN sucursales.ciudad%TYPE
) AS
BEGIN
    UPDATE sucursales 
    SET nombre_sucursal = s_nombre_sucursal, 
        direccion = s_direccion, 
        telefono = s_telefono, 
        ciudad = s_ciudad
    WHERE id_gimnasio = s_id_gimnasio;
    
    COMMIT;
END actualizar_sucursal;
/

CREATE OR REPLACE PROCEDURE eliminar_sucursal(
    s_id_gimnasio IN sucursales.id_gimnasio%TYPE
) AS
BEGIN
    DELETE FROM sucursales WHERE id_gimnasio = s_id_gimnasio;
    COMMIT;
END eliminar_sucursal;
/

/*SP Equipos*/

CREATE OR REPLACE PROCEDURE registrar_equipo_gimnasio(
    e_nombre IN equipos_gimnasio.nombre%TYPE,
    e_tipo IN equipos_gimnasio.tipo%TYPE,
    e_estado IN equipos_gimnasio.estado%TYPE,
    e_fecha_compra IN VARCHAR2,
    e_id_gimnasio IN equipos_gimnasio.id_gimnasio%TYPE
) AS
BEGIN

    INSERT INTO equipos_gimnasio (id_equipo, nombre, tipo, estado, fecha_compra, id_gimnasio)
    SELECT equipos_gimnasio_seq.NEXTVAL, e_nombre, e_tipo, e_estado, 
           TO_DATE(e_fecha_compra, 'YYYY-MM-DD'), e_id_gimnasio FROM dual
    WHERE EXISTS (SELECT 1 FROM sucursales WHERE id_gimnasio = e_id_gimnasio);
    
    COMMIT;
END registrar_equipo_gimnasio;
/

CREATE OR REPLACE PROCEDURE actualizar_equipo (
    e_id_equipo IN equipos_gimnasio.id_equipo%TYPE,
    e_nombre IN equipos_gimnasio.nombre%TYPE,
    e_tipo IN equipos_gimnasio.tipo%TYPE,
    e_estado IN equipos_gimnasio.estado%TYPE,
    e_fecha_compra IN VARCHAR2,
    e_id_gimnasio IN equipos_gimnasio.id_gimnasio%TYPE
) AS
BEGIN
    UPDATE equipos_gimnasio 
    SET nombre = e_nombre, 
        tipo = e_tipo, 
        estado = e_estado, 
        fecha_compra = TO_DATE(e_fecha_compra, 'YYYY-MM-DD'), 
        id_gimnasio = e_id_gimnasio
    WHERE id_equipo = e_id_equipo;
    
    COMMIT;
END actualizar_equipo;
/

CREATE OR REPLACE PROCEDURE eliminar_equipo (
    e_id_equipo IN equipos_gimnasio.id_equipo%TYPE
) AS
BEGIN
    DELETE FROM equipos_gimnasio 
    WHERE id_equipo = e_id_equipo;
    
    COMMIT;
END eliminar_equipo;
/

/*SP Mantenimiento*/

CREATE OR REPLACE PROCEDURE registrar_mantenimiento(
    m_id_equipo IN mantenimiento_equipos.id_equipo%TYPE,
    m_fecha_mantenimiento IN VARCHAR2,
    m_descripcion IN mantenimiento_equipos.descripcion%TYPE,
    m_estado IN mantenimiento_equipos.estado%TYPE
) AS
BEGIN
    INSERT INTO mantenimiento_equipos (id_mantenimiento, id_equipo, fecha_mantenimiento, descripcion, estado)
    VALUES (mantenimiento_equipos_seq.NEXTVAL, m_id_equipo, 
            TO_DATE(m_fecha_mantenimiento, 'YYYY-MM-DD'), m_descripcion, m_estado);
            
    UPDATE equipos_gimnasio
    SET estado = m_estado
    WHERE id_equipo = m_id_equipo;
    
    COMMIT;
END registrar_mantenimiento;
/

CREATE OR REPLACE PROCEDURE actualizar_mantenimiento (
    m_id_mantenimiento IN mantenimiento_equipos.id_mantenimiento%TYPE,
    m_id_equipo IN mantenimiento_equipos.id_equipo%TYPE,
    m_fecha_mantenimiento IN VARCHAR2,
    m_descripcion IN mantenimiento_equipos.descripcion%TYPE,
    m_estado IN mantenimiento_equipos.estado%TYPE
) AS
BEGIN
    UPDATE mantenimiento_equipos 
    SET id_equipo = m_id_equipo, 
        fecha_mantenimiento = TO_DATE(m_fecha_mantenimiento, 'YYYY-MM-DD'), 
        descripcion = m_descripcion, 
        estado = m_estado
    WHERE id_mantenimiento = m_id_mantenimiento;
    
    COMMIT;
END actualizar_mantenimiento;
/

CREATE OR REPLACE PROCEDURE eliminar_mantenimiento (
    m_id_mantenimiento IN mantenimiento_equipos.id_mantenimiento%TYPE
) AS
BEGIN
    DELETE FROM mantenimiento_equipos 
    WHERE id_mantenimiento = m_id_mantenimiento;
    
    COMMIT;
END eliminar_mantenimiento;
/

/*SP Productos de la tienda*/

CREATE OR REPLACE PROCEDURE registrar_producto (
    p_nombre_producto IN productos_tienda.nombre_producto%TYPE,
    p_precio IN productos_tienda.precio%TYPE,
    p_stock IN productos_tienda.stock%TYPE,
    p_tipo_producto IN productos_tienda.tipo_producto%TYPE
) AS
BEGIN
    INSERT INTO productos_tienda (id_producto, nombre_producto, precio, stock, 
    tipo_producto)
    VALUES (productos_tienda_seq.NEXTVAL, p_nombre_producto, p_precio, p_stock, 
    p_tipo_producto);
    
    COMMIT;
END registrar_producto;
/

CREATE OR REPLACE PROCEDURE actualizar_producto (
    p_id_producto IN productos_tienda.id_producto%TYPE,
    p_nombre_producto IN productos_tienda.nombre_producto%TYPE,
    p_precio IN productos_tienda.precio%TYPE,
    p_stock IN productos_tienda.stock%TYPE,
    p_tipo_producto IN productos_tienda.tipo_producto%TYPE
) AS
BEGIN
    UPDATE productos_tienda 
    SET nombre_producto = p_nombre_producto, 
        precio = p_precio, 
        stock = p_stock, 
        tipo_producto = p_tipo_producto WHERE id_producto = p_id_producto;
    
    COMMIT;
END actualizar_producto;
/

CREATE OR REPLACE PROCEDURE eliminar_producto (
    p_id_producto IN productos_tienda.id_producto%TYPE
) AS
BEGIN
    DELETE FROM productos_tienda WHERE id_producto = p_id_producto;
    
    COMMIT;
END eliminar_producto;
/

/*SP Registrar venta*/

CREATE OR REPLACE PROCEDURE registrar_venta(
    v_cedula IN ventas_tienda.cedula%TYPE,
    v_id_producto IN ventas_tienda.id_producto%TYPE,
    v_cantidad IN ventas_tienda.cantidad%TYPE,
    v_total IN ventas_tienda.total%TYPE,
    v_fecha_venta IN VARCHAR2
) AS
BEGIN
    INSERT INTO ventas_tienda (id_venta, cedula, id_producto, cantidad, total, fecha_venta)
    VALUES (ventas_tienda_seq.NEXTVAL, v_cedula, v_id_producto, v_cantidad, v_total, 
            TO_DATE(v_fecha_venta, 'YYYY-MM-DD'));
    
    UPDATE productos_tienda 
    SET stock = stock - v_cantidad 
    WHERE id_producto = v_id_producto;
    
    COMMIT;
END registrar_venta;
/

CREATE OR REPLACE PROCEDURE actualizar_venta_tienda (
    v_id_venta IN ventas_tienda.id_venta%TYPE,
    v_cedula IN ventas_tienda.cedula%TYPE,
    v_id_producto IN ventas_tienda.id_producto%TYPE,
    v_cantidad IN ventas_tienda.cantidad%TYPE,
    v_total IN ventas_tienda.total%TYPE,
    v_fecha_venta IN VARCHAR2
) AS
BEGIN
    UPDATE ventas_tienda 
    SET cedula = v_cedula, 
        id_producto = v_id_producto, 
        cantidad = v_cantidad, 
        total = v_total, 
        fecha_venta = TO_DATE(v_fecha_venta, 'YYYY-MM-DD')
   WHERE id_venta = v_id_venta;
    
    COMMIT;
END actualizar_venta_tienda;
/

CREATE OR REPLACE PROCEDURE eliminar_venta_tienda (
     v_id_venta IN ventas_tienda.id_venta%TYPE
) AS
    var_id_producto ventas_tienda.id_producto%TYPE;
    var_cantidad ventas_tienda.cantidad%TYPE;
    
BEGIN
    SELECT id_producto, cantidad 
    INTO var_id_producto, var_cantidad FROM ventas_tienda
    WHERE id_venta = v_id_venta;
    
    DELETE FROM ventas_tienda 
    WHERE id_venta = v_id_venta;
    
    UPDATE productos_tienda SET stock = stock + var_cantidad
    WHERE id_producto = var_id_producto;
    
    COMMIT;
END eliminar_venta_tienda;
/

/*SP pagos*/

CREATE OR REPLACE PROCEDURE registrar_pago(
    p_id_miembro IN pagos.id_miembro%TYPE,
    p_fecha_pago IN VARCHAR2,
    p_monto IN pagos.monto%TYPE,
    p_metodo_pago IN pagos.metodo_pago%TYPE
) AS
BEGIN

    INSERT INTO pagos (id_pago, id_miembro, fecha_pago, monto, metodo_pago)
    VALUES (pagos_seq.NEXTVAL, p_id_miembro, 
            TO_DATE(p_fecha_pago, 'YYYY-MM-DD'), p_monto, p_metodo_pago);

    UPDATE miembros 
    SET activo = 'y',
        costo_mensual = p_monto
    WHERE id_miembro = p_id_miembro;
    
    COMMIT;
    
END registrar_pago;
/

CREATE OR REPLACE PROCEDURE actualizar_pago(
    p_id_pago IN pagos.id_pago%TYPE,
    p_id_miembro IN pagos.id_miembro%TYPE,
    p_monto IN pagos.monto%TYPE,
    p_metodo_pago IN pagos.metodo_pago%TYPE,
    p_fecha_pago IN VARCHAR2
) AS
BEGIN
    UPDATE pagos 
    SET id_miembro = p_id_miembro, 
        monto = p_monto, 
        metodo_pago = p_metodo_pago, 
        fecha_pago = TO_DATE(p_fecha_pago, 'YYYY-MM-DD') WHERE id_pago = p_id_pago;
    
    UPDATE miembros 
    SET activo = 'y', 
        costo_mensual = p_monto WHERE id_miembro = p_id_miembro;
    
    COMMIT;
END actualizar_pago;
/

CREATE OR REPLACE PROCEDURE eliminar_pago (
    p_id_pago IN pagos.id_pago%TYPE
) AS
BEGIN
    UPDATE miembros m SET m.activo = 'n'
    WHERE m.id_miembro = (SELECT p.id_miembro FROM pagos p 
    WHERE p.id_pago = p_id_pago);
    
    DELETE FROM pagos WHERE id_pago = p_id_pago;
    
    COMMIT;
END eliminar_pago;
/


/*cursores*/
/*Clientes*/

CREATE OR REPLACE FUNCTION obtener_cliente_por_cedula(
    p_cedula IN clientes.cedula%TYPE
) RETURN SYS_REFCURSOR
IS
    v_cursor SYS_REFCURSOR;
BEGIN
    OPEN v_cursor FOR
        SELECT * FROM clientes WHERE cedula = p_cedula;
    
    RETURN v_cursor;

END obtener_cliente_por_cedula;

/*Miembros*/

CREATE OR REPLACE FUNCTION obtener_miembro_por_cedula(
    i_cedula IN miembros.cedula%TYPE
) RETURN SYS_REFCURSOR
IS
    v_cursor SYS_REFCURSOR;
BEGIN
    OPEN v_cursor FOR
        SELECT * FROM miembros WHERE cedula = i_cedula;
    
    RETURN v_cursor;
END obtener_miembro_por_cedula;
/

/*Instructores*/

CREATE OR REPLACE FUNCTION obtener_instructores
RETURN SYS_REFCURSOR
AS
  cur_instructores SYS_REFCURSOR;
BEGIN
  OPEN cur_instructores FOR
    SELECT id_instructor, nombre, especialidad, telefono, correo, salario
    FROM instructores;
  
  RETURN cur_instructores;
END;
/

CREATE OR REPLACE FUNCTION obtener_instructor_por_id(
    i_id_instructor IN instructores.id_instructor%TYPE
) RETURN SYS_REFCURSOR
IS
    v_cursor SYS_REFCURSOR;
BEGIN
    OPEN v_cursor FOR
        SELECT * FROM instructores WHERE id_instructor = i_id_instructor;
    
    RETURN v_cursor;

END obtener_instructor_por_id;
/

/*Clases*/

CREATE OR REPLACE FUNCTION obtener_clases
RETURN SYS_REFCURSOR
AS
  cur_clases SYS_REFCURSOR;
BEGIN
  OPEN cur_clases FOR
    SELECT id_clase, nombre_clase, descripcion, id_instructor
    FROM clases;
  
  RETURN cur_clases;
END;
/

CREATE OR REPLACE FUNCTION obtener_clase_por_id(
    i_id_clase IN clases.id_clase%TYPE
) RETURN SYS_REFCURSOR
IS
    v_cursor SYS_REFCURSOR;
BEGIN
    OPEN v_cursor FOR
        SELECT * FROM clases WHERE id_clase = i_id_clase;
    
    RETURN v_cursor;

END obtener_clase_por_id;
/

/*Equipos Gimnasio*/


CREATE OR REPLACE FUNCTION obtener_equipos_G1
RETURN SYS_REFCURSOR
AS
  cur_equipos SYS_REFCURSOR;
BEGIN
  OPEN cur_equipos FOR
    SELECT id_equipo, nombre, tipo, estado, fecha_compra, id_gimnasio
    FROM equipos_gimnasio where id_gimnasio = 1;
  
  RETURN cur_equipos;
END;
/

CREATE OR REPLACE FUNCTION obtener_equipos_G2
RETURN SYS_REFCURSOR
AS
  cur_equipos SYS_REFCURSOR;
BEGIN
  OPEN cur_equipos FOR
    SELECT id_equipo, nombre, tipo, estado, fecha_compra, id_gimnasio
    FROM equipos_gimnasio where id_gimnasio = 2;
  
  RETURN cur_equipos;
END;
/

CREATE OR REPLACE FUNCTION obtener_equipos_por_id(
    i_id_equipo IN equipos_gimnasio.id_equipo%TYPE
) RETURN SYS_REFCURSOR
IS
    v_cursor SYS_REFCURSOR;
BEGIN
    OPEN v_cursor FOR
        SELECT * FROM equipos_gimnasio WHERE id_equipo = i_id_equipo;
    
    RETURN v_cursor;

END obtener_equipos_por_id;
/

/*Mantenimiento*/

CREATE OR REPLACE FUNCTION obtener_mantenimiento_por_id(
    i_id_mantenimiento IN mantenimiento_equipos.id_mantenimiento%TYPE
) RETURN SYS_REFCURSOR
IS
    v_cursor SYS_REFCURSOR;
BEGIN
    OPEN v_cursor FOR
        SELECT * FROM mantenimiento_equipos WHERE id_mantenimiento = i_id_mantenimiento;
    
    RETURN v_cursor;

END obtener_mantenimiento_por_id;
/

/*Producto*/

CREATE OR REPLACE FUNCTION obtener_productos
RETURN SYS_REFCURSOR
AS
  cur_productos SYS_REFCURSOR;
BEGIN
  OPEN cur_productos FOR
    SELECT id_producto, nombre_producto, precio, stock, tipo_producto
    FROM productos_tienda;
  
  RETURN cur_productos;
END;
/

CREATE OR REPLACE FUNCTION obtener_producto_nombre
RETURN SYS_REFCURSOR
AS
  cur_productos SYS_REFCURSOR;
BEGIN
  OPEN cur_productos FOR
    SELECT id_producto, nombre_producto
    FROM productos_tienda;
  
  RETURN cur_productos;
END;
/

CREATE OR REPLACE FUNCTION obtener_producto_por_id(
    i_id_producto IN productos_tienda.id_producto%TYPE
) RETURN SYS_REFCURSOR
IS
    v_cursor SYS_REFCURSOR;
BEGIN
    OPEN v_cursor FOR
        SELECT * FROM productos_tienda WHERE id_producto = i_id_producto;
    
    RETURN v_cursor;

END obtener_producto_por_id;
/

/*ventas producto*/

CREATE OR REPLACE FUNCTION obtener_venta_por_id(
    i_id_venta IN ventas_tienda.id_venta%TYPE
) RETURN SYS_REFCURSOR
IS
    v_cursor SYS_REFCURSOR;
BEGIN
    OPEN v_cursor FOR
        SELECT v.*, p.nombre_producto 
        FROM ventas_tienda v
        JOIN productos_tienda p ON v.id_producto = p.id_producto
        WHERE v.id_venta = i_id_venta;
    
    RETURN v_cursor;
END obtener_venta_por_id;
/

/*Pago*/

CREATE OR REPLACE FUNCTION obtener_pago_por_id(
    i_id_pago IN pagos.id_pago%TYPE
) RETURN SYS_REFCURSOR
IS
    v_cursor SYS_REFCURSOR;
BEGIN
    OPEN v_cursor FOR
        SELECT * FROM pagos WHERE id_pago = i_id_pago;
    
    RETURN v_cursor;

END obtener_pago_por_id;
/

/*Sucursales*/

CREATE OR REPLACE FUNCTION obtener_sucursal_por_id(
    s_id_gimnasio IN sucursales.id_gimnasio%TYPE
) RETURN SYS_REFCURSOR
IS
    v_cursor SYS_REFCURSOR;
BEGIN
    OPEN v_cursor FOR
        SELECT * FROM sucursales WHERE id_gimnasio = s_id_gimnasio;
    
    RETURN v_cursor;
END obtener_sucursal_por_id;
/

/*FUNCIONES ESPECIALES*/


CREATE OR REPLACE FUNCTION calcular_totalDeLaVenta(
    p_id_producto IN productos_tienda.id_producto%TYPE,
    p_cantidad IN NUMBER
) RETURN NUMBER
IS
    v_precio productos_tienda.precio%TYPE;
    v_total NUMBER(10,2);
    v_stock productos_tienda.stock%TYPE;
BEGIN

    SELECT precio, stock INTO v_precio, v_stock
    FROM productos_tienda
    WHERE id_producto = p_id_producto;
    

    IF p_cantidad > v_stock THEN
        RAISE_APPLICATION_ERROR(-20001, 'Stock insuficiente. Disponible: ' || v_stock);
    END IF;

    v_total := v_precio * p_cantidad;
    
    RETURN v_total;
    
END calcular_totalDeLaVenta;
/

/*paquetes*/
/*Paquete de Clientes*/

CREATE OR REPLACE PACKAGE paquete_clientes AS
    PROCEDURE insertar_cliente(
        p_cedula IN VARCHAR2,
        p_nombre IN VARCHAR2,
        p_apellido IN VARCHAR2,
        p_correo IN VARCHAR2,
        p_telefono IN VARCHAR2,
        p_direccion IN VARCHAR2,
        p_fecha_registro IN DATE
    );

    PROCEDURE actualizar_cliente(
        p_cedula        IN clientes.cedula%TYPE,
        p_nombre        IN clientes.nombre%TYPE,
        p_apellido      IN clientes.apellido%TYPE,
        p_correo        IN clientes.correo%TYPE,
        p_telefono      IN clientes.telefono%TYPE,
        p_direccion     IN clientes.direccion%TYPE,
        p_fecha_registro IN VARCHAR2
    );

    PROCEDURE eliminar_cliente(
        c_cedula IN clientes.cedula%TYPE
    );

    FUNCTION obtener_cliente_por_cedula(
        p_cedula IN clientes.cedula%TYPE
    ) RETURN SYS_REFCURSOR;
END paquete_clientes;

CREATE OR REPLACE PACKAGE BODY paquete_clientes AS

    PROCEDURE insertar_cliente(
        p_cedula IN VARCHAR2,
        p_nombre IN VARCHAR2,
        p_apellido IN VARCHAR2,
        p_correo IN VARCHAR2,
        p_telefono IN VARCHAR2,
        p_direccion IN VARCHAR2,
        p_fecha_registro IN DATE
    ) IS
    BEGIN
        INSERT INTO clientes (cedula, nombre, apellido, correo, telefono, direccion, fecha_registro)
        VALUES (p_cedula, p_nombre, p_apellido, p_correo, p_telefono, p_direccion, p_fecha_registro);
        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END insertar_cliente;

    PROCEDURE actualizar_cliente(
        p_cedula        IN clientes.cedula%TYPE,
        p_nombre        IN clientes.nombre%TYPE,
        p_apellido      IN clientes.apellido%TYPE,
        p_correo        IN clientes.correo%TYPE,
        p_telefono      IN clientes.telefono%TYPE,
        p_direccion     IN clientes.direccion%TYPE,
        p_fecha_registro IN VARCHAR2
    ) IS
    BEGIN
        UPDATE clientes 
        SET nombre = p_nombre, 
            apellido = p_apellido, 
            correo = p_correo, 
            telefono = p_telefono, 
            direccion = p_direccion, 
            fecha_registro = TO_DATE(p_fecha_registro, 'YYYY-MM-DD')
        WHERE cedula = p_cedula;

        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END actualizar_cliente;

    PROCEDURE eliminar_cliente(
        c_cedula IN clientes.cedula%TYPE
    ) IS
    BEGIN
        DELETE FROM clientes WHERE cedula = c_cedula;
        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END eliminar_cliente;

    FUNCTION obtener_cliente_por_cedula(
        p_cedula IN clientes.cedula%TYPE
    ) RETURN SYS_REFCURSOR IS
        v_cursor SYS_REFCURSOR;
    BEGIN
        OPEN v_cursor FOR
            SELECT * FROM clientes WHERE cedula = p_cedula;
        RETURN v_cursor;
    END obtener_cliente_por_cedula;

END paquete_clientes;

/*Paquete de miembros*/

CREATE OR REPLACE PACKAGE paquete_membresias AS
    PROCEDURE registrar_membresia(
        m_cedula IN VARCHAR2,
        m_costo_mensual IN NUMBER
    );

    PROCEDURE actualizar_membresia(
        m_cedula IN miembros.cedula%TYPE,
        m_costo_mensual IN miembros.costo_mensual%TYPE,
        m_estado IN miembros.activo%TYPE
    );

    PROCEDURE eliminar_membresia(
        m_cedula IN miembros.cedula%TYPE
    );

    FUNCTION obtener_miembro_por_cedula(
        i_cedula IN miembros.cedula%TYPE
    ) RETURN SYS_REFCURSOR;
END paquete_membresias;

CREATE OR REPLACE PACKAGE BODY paquete_membresias AS

    PROCEDURE registrar_membresia(
        m_cedula IN VARCHAR2,
        m_costo_mensual IN NUMBER
    ) AS
    BEGIN
        INSERT INTO miembros (id_miembro, cedula, costo_mensual)
        SELECT miembros_seq.NEXTVAL, m_cedula, m_costo_mensual
        FROM dual
        WHERE EXISTS (SELECT 1 FROM clientes WHERE cedula = m_cedula);

        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END registrar_membresia;

    PROCEDURE actualizar_membresia(
        m_cedula IN miembros.cedula%TYPE,
        m_costo_mensual IN miembros.costo_mensual%TYPE,
        m_estado IN miembros.activo%TYPE
    ) AS
    BEGIN
        UPDATE miembros 
        SET costo_mensual = m_costo_mensual, 
            activo = m_estado
        WHERE cedula = m_cedula;

        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END actualizar_membresia;

    PROCEDURE eliminar_membresia(
        m_cedula IN miembros.cedula%TYPE
    ) AS
    BEGIN
        DELETE FROM miembros 
        WHERE cedula = m_cedula;

        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END eliminar_membresia;

    FUNCTION obtener_miembro_por_cedula(
        i_cedula IN miembros.cedula%TYPE
    ) RETURN SYS_REFCURSOR IS
        v_cursor SYS_REFCURSOR;
    BEGIN
        OPEN v_cursor FOR
            SELECT * FROM miembros WHERE cedula = i_cedula;
        RETURN v_cursor;
    END obtener_miembro_por_cedula;

END paquete_membresias;

/*Paquete instructores*/

CREATE OR REPLACE PACKAGE paquete_instructores AS
    PROCEDURE registrar_instructor (
        ins_nombre IN instructores.nombre%TYPE,
        ins_especialidad IN instructores.especialidad%TYPE,
        ins_telefono IN instructores.telefono%TYPE,
        ins_correo IN instructores.correo%TYPE,
        ins_salario IN instructores.salario%TYPE
    );

    PROCEDURE actualizar_instructor (
        ins_id IN instructores.id_instructor%TYPE,
        ins_nombre IN instructores.nombre%TYPE,
        ins_especialidad IN instructores.especialidad%TYPE,
        ins_telefono IN instructores.telefono%TYPE,
        ins_correo IN instructores.correo%TYPE,
        ins_salario IN instructores.salario%TYPE
    );

    PROCEDURE eliminar_instructor (
        ins_id_instructor IN instructores.id_instructor%TYPE
    );

    FUNCTION obtener_instructores
        RETURN SYS_REFCURSOR;

    FUNCTION obtener_instructor_por_id (
        i_id_instructor IN instructores.id_instructor%TYPE
    ) RETURN SYS_REFCURSOR;
END paquete_instructores;

CREATE OR REPLACE PACKAGE BODY paquete_instructores AS

    PROCEDURE registrar_instructor (
        ins_nombre IN instructores.nombre%TYPE,
        ins_especialidad IN instructores.especialidad%TYPE,
        ins_telefono IN instructores.telefono%TYPE,
        ins_correo IN instructores.correo%TYPE,
        ins_salario IN instructores.salario%TYPE
    ) AS
    BEGIN
        INSERT INTO instructores (id_instructor, nombre, especialidad, telefono, correo, salario)
        VALUES (instructores_seq.NEXTVAL, ins_nombre, ins_especialidad, ins_telefono, ins_correo, ins_salario);
        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END registrar_instructor;

    PROCEDURE actualizar_instructor (
        ins_id IN instructores.id_instructor%TYPE,
        ins_nombre IN instructores.nombre%TYPE,
        ins_especialidad IN instructores.especialidad%TYPE,
        ins_telefono IN instructores.telefono%TYPE,
        ins_correo IN instructores.correo%TYPE,
        ins_salario IN instructores.salario%TYPE
    ) AS
    BEGIN
        UPDATE instructores 
        SET nombre = ins_nombre, 
            especialidad = ins_especialidad, 
            telefono = ins_telefono, 
            correo = ins_correo, 
            salario = ins_salario
        WHERE id_instructor = ins_id;
        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END actualizar_instructor;

    PROCEDURE eliminar_instructor (
        ins_id_instructor IN instructores.id_instructor%TYPE
    ) AS
    BEGIN
        DELETE FROM instructores 
        WHERE id_instructor = ins_id_instructor;
        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END eliminar_instructor;

    FUNCTION obtener_instructores
        RETURN SYS_REFCURSOR IS
        cur_instructores SYS_REFCURSOR;
    BEGIN
        OPEN cur_instructores FOR
            SELECT id_instructor, nombre, especialidad, telefono, correo, salario
            FROM instructores;
        RETURN cur_instructores;
    END obtener_instructores;

    FUNCTION obtener_instructor_por_id (
        i_id_instructor IN instructores.id_instructor%TYPE
    ) RETURN SYS_REFCURSOR IS
        v_cursor SYS_REFCURSOR;
    BEGIN
        OPEN v_cursor FOR
            SELECT * FROM instructores WHERE id_instructor = i_id_instructor;
        RETURN v_cursor;
    END obtener_instructor_por_id;

END paquete_instructores;

/*Paquete clases*/

CREATE OR REPLACE PACKAGE paquete_clases AS
    PROCEDURE registrar_clase(
        c_nombre_clase    IN clases.nombre_clase%TYPE,
        c_descripcion     IN clases.descripcion%TYPE,
        c_id_instructor   IN clases.id_instructor%TYPE
    );

    PROCEDURE actualizar_clase(
        c_id              IN clases.id_clase%TYPE,
        c_nombre_clase    IN clases.nombre_clase%TYPE,
        c_descripcion     IN clases.descripcion%TYPE,
        c_id_instructor   IN clases.id_instructor%TYPE
    );

    PROCEDURE eliminar_clase(
        c_id_clase        IN clases.id_clase%TYPE
    );

    FUNCTION obtener_clases
        RETURN SYS_REFCURSOR;

    FUNCTION obtener_clase_por_id(
        i_id_clase        IN clases.id_clase%TYPE
    ) RETURN SYS_REFCURSOR;
END paquete_clases;
/

CREATE OR REPLACE PACKAGE BODY paquete_clases AS

    PROCEDURE registrar_clase(
        c_nombre_clase    IN clases.nombre_clase%TYPE,
        c_descripcion     IN clases.descripcion%TYPE,
        c_id_instructor   IN clases.id_instructor%TYPE
    ) AS
        existe_instructor NUMBER;
    BEGIN
        SELECT COUNT(*) INTO existe_instructor
        FROM instructores
        WHERE id_instructor = c_id_instructor;

        IF existe_instructor = 0 THEN
            RETURN;
        END IF;

        INSERT INTO clases (id_clase, nombre_clase, descripcion, id_instructor)
        VALUES (clases_seq.NEXTVAL, c_nombre_clase, c_descripcion, c_id_instructor);

        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END registrar_clase;

    PROCEDURE actualizar_clase(
        c_id              IN clases.id_clase%TYPE,
        c_nombre_clase    IN clases.nombre_clase%TYPE,
        c_descripcion     IN clases.descripcion%TYPE,
        c_id_instructor   IN clases.id_instructor%TYPE
    ) AS
    BEGIN
        UPDATE clases 
        SET nombre_clase = c_nombre_clase, 
            descripcion = c_descripcion, 
            id_instructor = c_id_instructor
        WHERE id_clase = c_id;

        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END actualizar_clase;

    PROCEDURE eliminar_clase(
        c_id_clase        IN clases.id_clase%TYPE
    ) AS
    BEGIN
        DELETE FROM clases 
        WHERE id_clase = c_id_clase;

        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END eliminar_clase;

    FUNCTION obtener_clases
        RETURN SYS_REFCURSOR IS
        cur_clases SYS_REFCURSOR;
    BEGIN
        OPEN cur_clases FOR
            SELECT id_clase, nombre_clase, descripcion, id_instructor
            FROM clases;

        RETURN cur_clases;
    END obtener_clases;

    FUNCTION obtener_clase_por_id(
        i_id_clase        IN clases.id_clase%TYPE
    ) RETURN SYS_REFCURSOR IS
        v_cursor SYS_REFCURSOR;
    BEGIN
        OPEN v_cursor FOR
            SELECT * FROM clases WHERE id_clase = i_id_clase;

        RETURN v_cursor;
    END obtener_clase_por_id;

END paquete_clases;
/

/*Paquete Equipos Gimnasio*/

CREATE OR REPLACE PACKAGE paquete_equipos_gimnasio AS
    PROCEDURE registrar_equipo_gimnasio(
        e_nombre        IN equipos_gimnasio.nombre%TYPE,
        e_tipo          IN equipos_gimnasio.tipo%TYPE,
        e_estado        IN equipos_gimnasio.estado%TYPE,
        e_fecha_compra  IN VARCHAR2,
        e_id_gimnasio   IN equipos_gimnasio.id_gimnasio%TYPE
    );

    PROCEDURE actualizar_equipo(
        e_id_equipo     IN equipos_gimnasio.id_equipo%TYPE,
        e_nombre        IN equipos_gimnasio.nombre%TYPE,
        e_tipo          IN equipos_gimnasio.tipo%TYPE,
        e_estado        IN equipos_gimnasio.estado%TYPE,
        e_fecha_compra  IN VARCHAR2,
        e_id_gimnasio   IN equipos_gimnasio.id_gimnasio%TYPE
    );

    PROCEDURE eliminar_equipo(
        e_id_equipo IN equipos_gimnasio.id_equipo%TYPE
    );

    FUNCTION obtener_equipos_G1
        RETURN SYS_REFCURSOR;

    FUNCTION obtener_equipos_G2
        RETURN SYS_REFCURSOR;

    FUNCTION obtener_equipos_por_id(
        i_id_equipo IN equipos_gimnasio.id_equipo%TYPE
    ) RETURN SYS_REFCURSOR;
END paquete_equipos_gimnasio;
/

CREATE OR REPLACE PACKAGE BODY paquete_equipos_gimnasio AS

    PROCEDURE registrar_equipo_gimnasio(
        e_nombre        IN equipos_gimnasio.nombre%TYPE,
        e_tipo          IN equipos_gimnasio.tipo%TYPE,
        e_estado        IN equipos_gimnasio.estado%TYPE,
        e_fecha_compra  IN VARCHAR2,
        e_id_gimnasio   IN equipos_gimnasio.id_gimnasio%TYPE
    ) AS
    BEGIN
        INSERT INTO equipos_gimnasio (id_equipo, nombre, tipo, estado, fecha_compra, id_gimnasio)
        SELECT equipos_gimnasio_seq.NEXTVAL, e_nombre, e_tipo, e_estado, 
               TO_DATE(e_fecha_compra, 'YYYY-MM-DD'), e_id_gimnasio FROM dual
        WHERE EXISTS (SELECT 1 FROM sucursales WHERE id_gimnasio = e_id_gimnasio);
        COMMIT;
    END registrar_equipo_gimnasio;

    PROCEDURE actualizar_equipo(
        e_id_equipo     IN equipos_gimnasio.id_equipo%TYPE,
        e_nombre        IN equipos_gimnasio.nombre%TYPE,
        e_tipo          IN equipos_gimnasio.tipo%TYPE,
        e_estado        IN equipos_gimnasio.estado%TYPE,
        e_fecha_compra  IN VARCHAR2,
        e_id_gimnasio   IN equipos_gimnasio.id_gimnasio%TYPE
    ) AS
    BEGIN
        UPDATE equipos_gimnasio 
        SET nombre = e_nombre, 
            tipo = e_tipo, 
            estado = e_estado, 
            fecha_compra = TO_DATE(e_fecha_compra, 'YYYY-MM-DD'), 
            id_gimnasio = e_id_gimnasio
        WHERE id_equipo = e_id_equipo;
        COMMIT;
    END actualizar_equipo;

    PROCEDURE eliminar_equipo(
        e_id_equipo IN equipos_gimnasio.id_equipo%TYPE
    ) AS
    BEGIN
        DELETE FROM equipos_gimnasio 
        WHERE id_equipo = e_id_equipo;
        COMMIT;
    END eliminar_equipo;

    FUNCTION obtener_equipos_G1
        RETURN SYS_REFCURSOR
    AS
        cur_equipos SYS_REFCURSOR;
    BEGIN
        OPEN cur_equipos FOR
            SELECT id_equipo, nombre, tipo, estado, fecha_compra, id_gimnasio
            FROM equipos_gimnasio WHERE id_gimnasio = 1;
        RETURN cur_equipos;
    END obtener_equipos_G1;

    FUNCTION obtener_equipos_G2
        RETURN SYS_REFCURSOR
    AS
        cur_equipos SYS_REFCURSOR;
    BEGIN
        OPEN cur_equipos FOR
            SELECT id_equipo, nombre, tipo, estado, fecha_compra, id_gimnasio
            FROM equipos_gimnasio WHERE id_gimnasio = 2;
        RETURN cur_equipos;
    END obtener_equipos_G2;

    FUNCTION obtener_equipos_por_id(
        i_id_equipo IN equipos_gimnasio.id_equipo%TYPE
    ) RETURN SYS_REFCURSOR
    AS
        v_cursor SYS_REFCURSOR;
    BEGIN
        OPEN v_cursor FOR
            SELECT * FROM equipos_gimnasio WHERE id_equipo = i_id_equipo;
        RETURN v_cursor;
    END obtener_equipos_por_id;

END paquete_equipos_gimnasio;
/

/*Paquete Mantenimiento de Equipos*/

CREATE OR REPLACE PACKAGE paquete_mantenimiento_equipos AS
    PROCEDURE registrar_mantenimiento(
        m_id_equipo IN mantenimiento_equipos.id_equipo%TYPE,
        m_fecha_mantenimiento IN VARCHAR2,
        m_descripcion IN mantenimiento_equipos.descripcion%TYPE,
        m_estado IN mantenimiento_equipos.estado%TYPE
    );

    PROCEDURE actualizar_mantenimiento(
        m_id_mantenimiento IN mantenimiento_equipos.id_mantenimiento%TYPE,
        m_id_equipo IN mantenimiento_equipos.id_equipo%TYPE,
        m_fecha_mantenimiento IN VARCHAR2,
        m_descripcion IN mantenimiento_equipos.descripcion%TYPE,
        m_estado IN mantenimiento_equipos.estado%TYPE
    );

    PROCEDURE eliminar_mantenimiento(
        m_id_mantenimiento IN mantenimiento_equipos.id_mantenimiento%TYPE
    );

    FUNCTION obtener_mantenimiento_por_id(
        i_id_mantenimiento IN mantenimiento_equipos.id_mantenimiento%TYPE
    ) RETURN SYS_REFCURSOR;
END paquete_mantenimiento_equipos;
/

CREATE OR REPLACE PACKAGE BODY paquete_mantenimiento_equipos AS

    PROCEDURE registrar_mantenimiento(
        m_id_equipo IN mantenimiento_equipos.id_equipo%TYPE,
        m_fecha_mantenimiento IN VARCHAR2,
        m_descripcion IN mantenimiento_equipos.descripcion%TYPE,
        m_estado IN mantenimiento_equipos.estado%TYPE
    ) AS
    BEGIN
        INSERT INTO mantenimiento_equipos (id_mantenimiento, id_equipo, fecha_mantenimiento, descripcion, estado)
        VALUES (mantenimiento_equipos_seq.NEXTVAL, m_id_equipo, 
                TO_DATE(m_fecha_mantenimiento, 'YYYY-MM-DD'), m_descripcion, m_estado);
                
         UPDATE equipos_gimnasio
        SET estado = m_estado
        WHERE id_equipo = m_id_equipo;
        
        COMMIT;
    END registrar_mantenimiento;

    PROCEDURE actualizar_mantenimiento(
        m_id_mantenimiento IN mantenimiento_equipos.id_mantenimiento%TYPE,
        m_id_equipo IN mantenimiento_equipos.id_equipo%TYPE,
        m_fecha_mantenimiento IN VARCHAR2,
        m_descripcion IN mantenimiento_equipos.descripcion%TYPE,
        m_estado IN mantenimiento_equipos.estado%TYPE
    ) AS
    BEGIN
        UPDATE mantenimiento_equipos 
        SET id_equipo = m_id_equipo, 
            fecha_mantenimiento = TO_DATE(m_fecha_mantenimiento, 'YYYY-MM-DD'), 
            descripcion = m_descripcion, 
            estado = m_estado
        WHERE id_mantenimiento = m_id_mantenimiento;
        COMMIT;
    END actualizar_mantenimiento;

    PROCEDURE eliminar_mantenimiento(
        m_id_mantenimiento IN mantenimiento_equipos.id_mantenimiento%TYPE
    ) AS
    BEGIN
        DELETE FROM mantenimiento_equipos 
        WHERE id_mantenimiento = m_id_mantenimiento;
        COMMIT;
    END eliminar_mantenimiento;

    FUNCTION obtener_mantenimiento_por_id(
        i_id_mantenimiento IN mantenimiento_equipos.id_mantenimiento%TYPE
    ) RETURN SYS_REFCURSOR IS
        v_cursor SYS_REFCURSOR;
    BEGIN
        OPEN v_cursor FOR
            SELECT * FROM mantenimiento_equipos WHERE id_mantenimiento = i_id_mantenimiento;
        RETURN v_cursor;
    END obtener_mantenimiento_por_id;

END paquete_mantenimiento_equipos;
/

/*Paquete Productos*/

CREATE OR REPLACE PACKAGE paquete_productos IS
    PROCEDURE registrar_producto(
        p_nombre_producto IN productos_tienda.nombre_producto%TYPE,
        p_precio IN productos_tienda.precio%TYPE,
        p_stock IN productos_tienda.stock%TYPE,
        p_tipo_producto IN productos_tienda.tipo_producto%TYPE
    );

    PROCEDURE actualizar_producto(
        p_id_producto IN productos_tienda.id_producto%TYPE,
        p_nombre_producto IN productos_tienda.nombre_producto%TYPE,
        p_precio IN productos_tienda.precio%TYPE,
        p_stock IN productos_tienda.stock%TYPE,
        p_tipo_producto IN productos_tienda.tipo_producto%TYPE
    );

    PROCEDURE eliminar_producto(
        p_id_producto IN productos_tienda.id_producto%TYPE
    );

    FUNCTION obtener_productos RETURN SYS_REFCURSOR;

    FUNCTION obtener_producto_por_id(
        i_id_producto IN productos_tienda.id_producto%TYPE
    ) RETURN SYS_REFCURSOR;
END paquete_productos;
/

CREATE OR REPLACE PACKAGE BODY paquete_productos IS

    PROCEDURE registrar_producto(
        p_nombre_producto IN productos_tienda.nombre_producto%TYPE,
        p_precio IN productos_tienda.precio%TYPE,
        p_stock IN productos_tienda.stock%TYPE,
        p_tipo_producto IN productos_tienda.tipo_producto%TYPE
    ) IS
    BEGIN
        INSERT INTO productos_tienda (id_producto, nombre_producto, precio, stock, tipo_producto)
        VALUES (productos_tienda_seq.NEXTVAL, p_nombre_producto, p_precio, p_stock, p_tipo_producto);
        COMMIT;
    END registrar_producto;

    PROCEDURE actualizar_producto(
        p_id_producto IN productos_tienda.id_producto%TYPE,
        p_nombre_producto IN productos_tienda.nombre_producto%TYPE,
        p_precio IN productos_tienda.precio%TYPE,
        p_stock IN productos_tienda.stock%TYPE,
        p_tipo_producto IN productos_tienda.tipo_producto%TYPE
    ) IS
    BEGIN
        UPDATE productos_tienda
        SET nombre_producto = p_nombre_producto,
            precio = p_precio,
            stock = p_stock,
            tipo_producto = p_tipo_producto
        WHERE id_producto = p_id_producto;
        COMMIT;
    END actualizar_producto;

    PROCEDURE eliminar_producto(
        p_id_producto IN productos_tienda.id_producto%TYPE
    ) IS
    BEGIN
        DELETE FROM productos_tienda WHERE id_producto = p_id_producto;
        COMMIT;
    END eliminar_producto;

    FUNCTION obtener_productos RETURN SYS_REFCURSOR IS
        cur_productos SYS_REFCURSOR;
    BEGIN
        OPEN cur_productos FOR
            SELECT id_producto, nombre_producto, precio, stock, tipo_producto
            FROM productos_tienda;
        RETURN cur_productos;
    END obtener_productos;

    FUNCTION obtener_producto_por_id(
        i_id_producto IN productos_tienda.id_producto%TYPE
    ) RETURN SYS_REFCURSOR IS
        v_cursor SYS_REFCURSOR;
    BEGIN
        OPEN v_cursor FOR
            SELECT * FROM productos_tienda WHERE id_producto = i_id_producto;
        RETURN v_cursor;
    END obtener_producto_por_id;

END paquete_productos;
/

/*Paquete Ventas*/

CREATE OR REPLACE PACKAGE paquete_ventas AS

    PROCEDURE registrar_venta(
        v_cedula IN ventas_tienda.cedula%TYPE,
        v_id_producto IN ventas_tienda.id_producto%TYPE,
        v_cantidad IN ventas_tienda.cantidad%TYPE,
        v_total IN ventas_tienda.total%TYPE,
        v_fecha_venta IN VARCHAR2
    );
    
    PROCEDURE actualizar_venta_tienda(
        v_id_venta IN ventas_tienda.id_venta%TYPE,
        v_cedula IN ventas_tienda.cedula%TYPE,
        v_id_producto IN ventas_tienda.id_producto%TYPE,
        v_cantidad IN ventas_tienda.cantidad%TYPE,
        v_total IN ventas_tienda.total%TYPE,
        v_fecha_venta IN VARCHAR2
    );
    
    PROCEDURE eliminar_venta_tienda(
        v_id_venta IN ventas_tienda.id_venta%TYPE
    );
    
    FUNCTION obtener_venta_por_id(
        i_id_venta IN ventas_tienda.id_venta%TYPE
    ) RETURN SYS_REFCURSOR;
    
    FUNCTION calcular_totalDeLaVenta(
        p_id_producto IN productos_tienda.id_producto%TYPE,
        p_cantidad IN NUMBER
    ) RETURN NUMBER;
    
END paquete_ventas;
/

CREATE OR REPLACE PACKAGE BODY paquete_ventas AS

    PROCEDURE registrar_venta(
        v_cedula IN ventas_tienda.cedula%TYPE,
        v_id_producto IN ventas_tienda.id_producto%TYPE,
        v_cantidad IN ventas_tienda.cantidad%TYPE,
        v_total IN ventas_tienda.total%TYPE,
        v_fecha_venta IN VARCHAR2
    ) AS
    BEGIN
        INSERT INTO ventas_tienda (id_venta, cedula, id_producto, cantidad, total, fecha_venta)
        VALUES (ventas_tienda_seq.NEXTVAL, v_cedula, v_id_producto, v_cantidad, v_total, 
                TO_DATE(v_fecha_venta, 'YYYY-MM-DD'));
        
        UPDATE productos_tienda 
        SET stock = stock - v_cantidad 
        WHERE id_producto = v_id_producto;
        
        COMMIT;
    END registrar_venta;
    
    PROCEDURE actualizar_venta_tienda (
        v_id_venta IN ventas_tienda.id_venta%TYPE,
        v_cedula IN ventas_tienda.cedula%TYPE,
        v_id_producto IN ventas_tienda.id_producto%TYPE,
        v_cantidad IN ventas_tienda.cantidad%TYPE,
        v_total IN ventas_tienda.total%TYPE,
        v_fecha_venta IN VARCHAR2
    ) AS
    BEGIN
        UPDATE ventas_tienda 
        SET cedula = v_cedula, 
            id_producto = v_id_producto, 
            cantidad = v_cantidad, 
            total = v_total, 
            fecha_venta = TO_DATE(v_fecha_venta, 'YYYY-MM-DD')
       WHERE id_venta = v_id_venta;
        
        COMMIT;
    END actualizar_venta_tienda;
    
    PROCEDURE eliminar_venta_tienda (
         v_id_venta IN ventas_tienda.id_venta%TYPE
    ) AS
        var_id_producto ventas_tienda.id_producto%TYPE;
        var_cantidad ventas_tienda.cantidad%TYPE;
        
    BEGIN
        SELECT id_producto, cantidad 
        INTO var_id_producto, var_cantidad FROM ventas_tienda
        WHERE id_venta = v_id_venta;
        
        DELETE FROM ventas_tienda 
        WHERE id_venta = v_id_venta;
        
        UPDATE productos_tienda SET stock = stock + var_cantidad
        WHERE id_producto = var_id_producto;
        
        COMMIT;
    END eliminar_venta_tienda;
    
    FUNCTION obtener_venta_por_id(
        i_id_venta IN ventas_tienda.id_venta%TYPE
    ) RETURN SYS_REFCURSOR
    IS
        v_cursor SYS_REFCURSOR;
    BEGIN
        OPEN v_cursor FOR
            SELECT * FROM ventas_tienda WHERE id_venta = i_id_venta;
        
        RETURN v_cursor;
    END obtener_venta_por_id;
    
    FUNCTION calcular_totalDeLaVenta(
        p_id_producto IN productos_tienda.id_producto%TYPE,
        p_cantidad IN NUMBER
    ) RETURN NUMBER
    IS
        v_precio productos_tienda.precio%TYPE;
        v_total NUMBER(10,2);
        v_stock productos_tienda.stock%TYPE;
    BEGIN
        SELECT precio, stock INTO v_precio, v_stock
        FROM productos_tienda
        WHERE id_producto = p_id_producto;
        
        IF p_cantidad > v_stock THEN
            RAISE_APPLICATION_ERROR(-20001, 'Stock insuficiente. Disponible: ' || v_stock);
        END IF;

        v_total := v_precio * p_cantidad;
        
        RETURN v_total;
    END calcular_totalDeLaVenta;
    
END paquete_ventas;
/

/*Paquete Pagos*/

CREATE OR REPLACE PACKAGE paquete_pagos AS
    PROCEDURE registrar_pago(
        p_id_miembro IN pagos.id_miembro%TYPE,
        p_fecha_pago IN VARCHAR2,
        p_monto IN pagos.monto%TYPE,
        p_metodo_pago IN pagos.metodo_pago%TYPE
    );
    
    PROCEDURE actualizar_pago(
        p_id_pago IN pagos.id_pago%TYPE,
        p_id_miembro IN pagos.id_miembro%TYPE,
        p_monto IN pagos.monto%TYPE,
        p_metodo_pago IN pagos.metodo_pago%TYPE,
        p_fecha_pago IN VARCHAR2
    );
    
    PROCEDURE eliminar_pago (
        p_id_pago IN pagos.id_pago%TYPE
    );
    
    FUNCTION obtener_pago_por_id(
        i_id_pago IN pagos.id_pago%TYPE
    ) RETURN SYS_REFCURSOR;
END paquete_pagos;
/

CREATE OR REPLACE PACKAGE BODY paquete_pagos AS
    PROCEDURE registrar_pago(
        p_id_miembro IN pagos.id_miembro%TYPE,
        p_fecha_pago IN VARCHAR2,
        p_monto IN pagos.monto%TYPE,
        p_metodo_pago IN pagos.metodo_pago%TYPE
    ) AS
    BEGIN
        INSERT INTO pagos (id_pago, id_miembro, fecha_pago, monto, metodo_pago)
        VALUES (pagos_seq.NEXTVAL, p_id_miembro, 
                TO_DATE(p_fecha_pago, 'YYYY-MM-DD'), p_monto, p_metodo_pago);

        UPDATE miembros 
        SET activo = 'y',
            costo_mensual = p_monto
        WHERE id_miembro = p_id_miembro;
        
        COMMIT;
    END registrar_pago;
    
    PROCEDURE actualizar_pago(
        p_id_pago IN pagos.id_pago%TYPE,
        p_id_miembro IN pagos.id_miembro%TYPE,
        p_monto IN pagos.monto%TYPE,
        p_metodo_pago IN pagos.metodo_pago%TYPE,
        p_fecha_pago IN VARCHAR2
    ) AS
    BEGIN
        UPDATE pagos 
        SET id_miembro = p_id_miembro, 
            monto = p_monto, 
            metodo_pago = p_metodo_pago, 
            fecha_pago = TO_DATE(p_fecha_pago, 'YYYY-MM-DD') WHERE id_pago = p_id_pago;
        
        UPDATE miembros 
        SET activo = 'y', 
            costo_mensual = p_monto WHERE id_miembro = p_id_miembro;
        
        COMMIT;
    END actualizar_pago;
    
    PROCEDURE eliminar_pago (
        p_id_pago IN pagos.id_pago%TYPE
    ) AS
    BEGIN
        UPDATE miembros m SET m.activo = 'n'
        WHERE m.id_miembro = (SELECT p.id_miembro FROM pagos p 
        WHERE p.id_pago = p_id_pago);
        
        DELETE FROM pagos WHERE id_pago = p_id_pago;
        
        COMMIT;
    END eliminar_pago;
    
    FUNCTION obtener_pago_por_id(
        i_id_pago IN pagos.id_pago%TYPE
    ) RETURN SYS_REFCURSOR
    IS
        v_cursor SYS_REFCURSOR;
    BEGIN
        OPEN v_cursor FOR
            SELECT * FROM pagos WHERE id_pago = i_id_pago;
        
        RETURN v_cursor;
    END obtener_pago_por_id;
END paquete_pagos;
/

CREATE OR REPLACE PACKAGE paquete_sucursales AS
    PROCEDURE registrar_sucursal(
        s_nombre_sucursal IN sucursales.nombre_sucursal%TYPE,
        s_direccion       IN sucursales.direccion%TYPE,
        s_telefono        IN sucursales.telefono%TYPE,
        s_ciudad          IN sucursales.ciudad%TYPE
    );
    
    PROCEDURE actualizar_sucursal(
        s_id_gimnasio     IN sucursales.id_gimnasio%TYPE,
        s_nombre_sucursal IN sucursales.nombre_sucursal%TYPE,
        s_direccion       IN sucursales.direccion%TYPE,
        s_telefono        IN sucursales.telefono%TYPE,
        s_ciudad          IN sucursales.ciudad%TYPE
    );
    
    PROCEDURE eliminar_sucursal(
        s_id_gimnasio IN sucursales.id_gimnasio%TYPE
    );
    
    FUNCTION obtener_sucursal_por_id(
        s_id_gimnasio IN sucursales.id_gimnasio%TYPE
    ) RETURN SYS_REFCURSOR;
    
END paquete_sucursales;
/

CREATE OR REPLACE PACKAGE BODY paquete_sucursales AS

    PROCEDURE registrar_sucursal(
        s_nombre_sucursal IN sucursales.nombre_sucursal%TYPE,
        s_direccion       IN sucursales.direccion%TYPE,
        s_telefono        IN sucursales.telefono%TYPE,
        s_ciudad          IN sucursales.ciudad%TYPE
    ) AS
    BEGIN
        INSERT INTO sucursales (id_gimnasio, nombre_sucursal, direccion, telefono, ciudad)
        VALUES (sucursales_seq.NEXTVAL, s_nombre_sucursal, s_direccion, s_telefono, s_ciudad);
        
        COMMIT;
    END registrar_sucursal;

    
    PROCEDURE actualizar_sucursal(
        s_id_gimnasio     IN sucursales.id_gimnasio%TYPE,
        s_nombre_sucursal IN sucursales.nombre_sucursal%TYPE,
        s_direccion       IN sucursales.direccion%TYPE,
        s_telefono        IN sucursales.telefono%TYPE,
        s_ciudad          IN sucursales.ciudad%TYPE
    ) AS
    BEGIN
        UPDATE sucursales 
        SET nombre_sucursal = s_nombre_sucursal, 
            direccion = s_direccion, 
            telefono = s_telefono, 
            ciudad = s_ciudad
        WHERE id_gimnasio = s_id_gimnasio;
        
        COMMIT;
    END actualizar_sucursal;
    
    PROCEDURE eliminar_sucursal(
        s_id_gimnasio IN sucursales.id_gimnasio%TYPE
    ) AS
    BEGIN
        DELETE FROM sucursales WHERE id_gimnasio = s_id_gimnasio;
        COMMIT;
    END eliminar_sucursal;
    
    FUNCTION obtener_sucursal_por_id(
        s_id_gimnasio IN sucursales.id_gimnasio%TYPE
    ) RETURN SYS_REFCURSOR
    IS
        v_cursor SYS_REFCURSOR;
    BEGIN
        OPEN v_cursor FOR
            SELECT * FROM sucursales WHERE id_gimnasio = s_id_gimnasio;
        
        RETURN v_cursor;
    END obtener_sucursal_por_id;
    
    
END paquete_sucursales;
/



/*Triggers*/

CREATE OR REPLACE TRIGGER validacion_numero_telef_cliente
BEFORE INSERT OR UPDATE ON clientes
FOR EACH ROW
BEGIN
    IF :NEW.telefono IS NOT NULL THEN
        IF NOT REGEXP_LIKE(:NEW.telefono, '^[0-9]{8}$') THEN
            RAISE_APPLICATION_ERROR(-20001, 'El número de teléfono indicado debe contener solo dígitos numericos y tener 8 caracteres.');
        END IF;
    END IF;
END;
/

CREATE OR REPLACE TRIGGER validar_cantidad_venta
BEFORE INSERT OR UPDATE ON ventas_tienda
FOR EACH ROW
BEGIN
    IF :NEW.cantidad <= 0 THEN
        RAISE_APPLICATION_ERROR(-20002, 'La cantidad que desea comprar debe ser mayor que cero.');
    END IF;
END;
/

CREATE TABLE auditoria_ventas (
    id_auditoria NUMBER PRIMARY KEY,
    id_venta NUMBER,
    cedula VARCHAR2(15),
    id_producto NUMBER,
    cantidad NUMBER,
    total NUMBER,
    fecha_venta DATE,
    fecha_registro DATE
);

CREATE SEQUENCE auditoria_ventas_seq
START WITH 1
INCREMENT BY 1
NOCACHE;

CREATE OR REPLACE TRIGGER auditar_venta
AFTER INSERT ON ventas_tienda
FOR EACH ROW
BEGIN
    INSERT INTO auditoria_ventas (
        id_auditoria,
        id_venta,
        cedula,
        id_producto,
        cantidad,
        total,
        fecha_venta,
        fecha_registro
    ) VALUES (
        auditoria_ventas_seq.NEXTVAL,
        :NEW.id_venta,
        :NEW.cedula,
        :NEW.id_producto,
        :NEW.cantidad,
        :NEW.total,
        :NEW.fecha_venta,
        SYSDATE
    );
END;
/

CREATE OR REPLACE TRIGGER validacion_correos_clientes
BEFORE INSERT OR UPDATE ON clientes
FOR EACH ROW
BEGIN
    IF NOT REGEXP_LIKE(:NEW.correo, '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$') THEN
        RAISE_APPLICATION_ERROR(-20003, 'El formato del correo electrónico no es válido (clientes).');
    END IF;
END;
/

CREATE OR REPLACE TRIGGER validacion_correos_empleados
BEFORE INSERT OR UPDATE ON empleados
FOR EACH ROW
BEGIN
    IF NOT REGEXP_LIKE(:NEW.email, '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$') THEN
        RAISE_APPLICATION_ERROR(-20004, 'El formato del correo electrónico no es válido (empleados).');
    END IF;
END;
/
