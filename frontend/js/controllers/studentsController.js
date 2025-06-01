// controla el estado y aspecto de la página - módulo estudiantes

import { studentsAPI } from '../api/studentsAPI.js'; // importa la API de estudiantes

document.addEventListener('DOMContentLoaded', () => { // espera a que termine de cargar el DOM
    loadStudents(); // carga la tabla
    setupFormHandler(); // procesa el formulario
    setupCancelHandler(); // procesa el reset del formulario
});
  
function setupFormHandler() {
    const form = document.getElementById('studentForm'); // obtiene el formulario
    form.addEventListener('submit', async e => { // al enviar el formulario
        e.preventDefault(); // (?)
        const student = getFormData(); // obtiene los datos
    
        try {
            if (student.id) { // si la id está vacía - la id solo va a tener valor si se edita un estudiante
                await studentsAPI.update(student); // actualiza un estudiante
            } else {
                await studentsAPI.create(student); // crea un nuevo estudiante
            }
            clearForm(); // vacía el formulario
            loadStudents(); // recarga la tabla
        } catch (err) { // si se lanza un error desde la API (throw)
            let alertmsg = ''; // inicializa el mensaje 

            switch (err.message) { // contempla los distintos errores - en este caso no hay definido ningún error específico
                default:
                    alertmsg = 'Error en ' + (student.id ? 'UPDATE' : 'CREATE'); // (*) ¿hay forma de pasar el método como variable?
            }
            
            console.error('Error guardando estudiante:', alertmsg.toLowerCase()); // muestra el mensaje del error
            addAlert(alertmsg);
        }
    });
}

function setupCancelHandler() {
    const cancelBtn = document.getElementById('cancelBtn');
    cancelBtn.addEventListener('click', () => { // ejecuta una función al recibir un click
        document.getElementById('studentId').value = ''; // vacía la id (oculta) del estudiante
    });
}

function getFormData() {
    return {
        // obtiene los valores del formulario - 'trim()' elimina los espacios en blanco al principio y al final
        id: document.getElementById('studentId').value.trim(),
        fullname: document.getElementById('fullname').value.trim(),
        email: document.getElementById('email').value.trim(),
        age: parseInt(document.getElementById('age').value.trim(), 10)
    };
}
  
function clearForm() {
    document.getElementById('studentForm').reset(); // vacía el formulario
    document.getElementById('studentId').value = ''; // vacía la id (oculta)
}
  
async function loadStudents() {
    try {
        const students = await studentsAPI.fetchAll(); // invoca el método GET interno de la API
        renderStudentTable(students); // genera la tabla
    } catch (err) {
        console.error('Error cargando estudiantes:', err.message);
    }
}
  
function renderStudentTable(students) { // genera la tabla
    const tbody = document.getElementById('studentTableBody'); // obtiene el cuerpo de la tabla
    tbody.replaceChildren(); // vacía la tabla
    emptyAlert();
  
    students.forEach(student => { // ejecuta una función anónima por cada elemento (student) del array students
        const tr = document.createElement('tr'); // crea una fila
    
        // agrega celdas a la fila
        tr.appendChild(createCell(student.fullname));
        tr.appendChild(createCell(student.email));
        tr.appendChild(createCell(student.age.toString()));
        tr.appendChild(createActionsCell(student)); 
    
        tbody.appendChild(tr); // añade la fila a la tabla
    });
}
  
function createCell(text) { // crea una celda de información
    const td = document.createElement('td');
    td.textContent = text; // inserta la propiedad del estudiante correspondiente
    return td; // devuelve la celda
}
  
function createActionsCell(student) { // crea una celda de datos
    const td = document.createElement('td');
  
    // define el botón de edición
    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.addEventListener('click', () => fillForm(student)); // al ser presionado, rellena el formulario
  
    // define el botón de borrado
    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.addEventListener('click', () => confirmDelete(student.id)); // al ser presionado, elimina la fila
  
    // añade ambos botones a la celda
    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td; // devuelve la celda
}
  
function fillForm(student) { // rellena el formulario con la fila
    document.getElementById('studentId').value = student.id;
    document.getElementById('fullname').value = student.fullname;
    document.getElementById('email').value = student.email;
    document.getElementById('age').value = student.age;
}
  
async function confirmDelete(id) { // confirma la eliminación de fila
    if (!confirm('¿Estás seguro que deseas borrar este estudiante?')) return; // muestra un mensaje en pantalla - si no se confirma, no prosigue con la eliminación
  
    try {
        await studentsAPI.remove(id); // invoca el método DELETE interno de la API
        loadStudents(); // recarga la tabla
    } catch (err) {
        let alertmsg = '';

        switch (err.message) {
            case '1451': // caso 1451 - violación de restricción de clave foránea
                alertmsg = 'El estudiante no puede ser eliminado porque está inscrito en una materia';
                break;
            default: // caso general
                alertmsg = 'Error en DELETE';
        }

        console.error('Error al borrar estudiante:', alertmsg.toLowerCase());
        addAlert(alertmsg);
    }
}

function addAlert(message) {
    const alertbox = document.getElementById('alertbox');
    const alert = document.createElement('p');
    alert.textContent = message;
    alertbox.appendChild(alert);
    
    alert.addEventListener('click', () => alert.remove()); // al ser presionado, elimina la alerta
}

function emptyAlert() {
    const alertbox = document.getElementById('alertbox');
    alertbox.replaceChildren();
}
// (?) ¿cómo pueden modularizarse las alertas?