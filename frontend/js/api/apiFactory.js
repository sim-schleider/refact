// genera APIs reutilizables

export function createAPI(moduleName, config = {}) { // exporta una función - 'moduleName' guarda el nombre del módulo, 'config' permite configuraciones avanzadas
    const API_URL = config.urlOverride ?? `../../backend/server.php?module=${moduleName}`; // URL del código del lado del servidor

    async function sendJSON(method, data) { // envía datos del cliente al servidor - la función se utiliza más adelante
        const res = await fetch(API_URL, { // se guarda el resultado de un petición al servidor
            method, // explicita el método - no puede ser GET porque se está enviando información, no recibiendo
            headers: { 'Content-Type': 'application/json' }, // indica los encabezados (?)
            body: JSON.stringify(data) // indica los datos a ser enviados
        });

        const resobj =  await res.json(); // obtiene un JSON con la respuesta - (?) hay algún problema con hacerlo antes

        if (!res.ok) { // si ocurrió un error (res.ok == false)
            switch (resobj.errno) { // (*) solo trata el caso en DELETE
                case 1451: // caso 1451 - violación de restricción de clave foránea
                    switch (moduleName) { // (*) dependiendo de si la petición fue por estudiante o por materia
                        case "students": 
                            throw new Error("El estudiante no puede ser eliminado porque está inscrito en una materia");
                        case "courses":
                            throw new Error("La materia no puede ser eliminada porque tiene estudiantes inscritos");
                    }
                default:
                    throw new Error(`Error en ${method}`); // si el 'fetch' no fue exitoso, lanza un error - el error debe ser recibido con 'catch'
            }
        }     
        return resobj;
    }

    return { // devolución de la API
        async fetchAll() { // obtiene todos los registros del módulo
            const res = await fetch(API_URL); // hace una petición GET - no requiere de la función anterior
            if (!res.ok) throw new Error("No se pudieron obtener los datos");
            return await res.json();
        }, 
        async create(data) { // crea un elemento
            return await sendJSON('POST', data); // (?) de dónde sale 'data'?
        }, 
        async update(data) { // actualiza un elemento
            return await sendJSON('PUT', data);
        }, 
        async remove(id) { // elimina un elemento
            return await sendJSON('DELETE', { id });
        }
    };
}
