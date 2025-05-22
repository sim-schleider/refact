export function createAPI(moduleName) 
{
    const API_URL = `../../backend/server.php?module=${moduleName}`;//HardCoding
  
    async function sendJSON(method, data) 
    {
        const res = await fetch(API_URL, 
        {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        if (!res.ok) throw new Error(`Error en ${method}`);
        return await res.json();
    }
  
    return {
        async fetchAll() 
        {
            const res = await fetch(API_URL);
            if (!res.ok) throw new Error("No se pudieron obtener los datos");
            return await res.json();
        },
        async create(data) 
        {
            return await sendJSON('POST', data);
        },
        async update(data) 
        {
            return await sendJSON('PUT', data);
        },
        async remove(id) 
        {
            return await sendJSON('DELETE', { id });
        }
    };
  }