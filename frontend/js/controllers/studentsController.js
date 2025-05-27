// controla el estado y aspecto de la página - módulo estudiantes

import { studentsAPI } from '../api/studentsAPI.js'; // importa la API de estudiantes

document.addEventListener('DOMContentLoaded', () => { // espera a que termine de cargar el DOM
    loadStudents(); // carga la tabla
    setupFormHandler(); // procesa el formulario
});
  
function setupFormHandler() {
    const form = document.getElementById('studentForm'); // obtiene el formulario
    form.addEventListener('submit', async e => { // al enviar el formulario
        e.preventDefault(); // (?)
        const student = getFormData(); // obtiene los datos
    
        try {
            if (student.id) { // (?)
                await studentsAPI.update(student); // actualiza un estudiante
            } else {
                await studentsAPI.create(student); // crea un nuevo estudiante
            }
            clearForm(); // vacía el formulario
            loadStudents(); // recarga la tabla
        } catch (err) { // si se lanza un error desde la API (throw)
            console.error(err.message); // muestra el mensaje del error
        }
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
    if (!confirm('¿Estás seguro que deseas borrar este estudiante?')) return; // muestra un mensaje en pantalla - si no se confirma, no prosigue con la eliminación - (*)
  
    try {
        await studentsAPI.remove(id); // invoca el método DELETE interno de la API
        loadStudents(); // recarga la tabla
    } catch (err) {
        console.error('Error al borrar:', err.message);
    }
}
  