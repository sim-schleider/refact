import { studentsAPI } from '../api/studentsAPI.js';
import { coursesAPI } from '../api/coursesAPI.js';
import { studentsCoursesAPI } from '../api/studentsCoursesAPI.js';


document.addEventListener('DOMContentLoaded', () => 
{
    initSelects();
    setupFormHandler();
    loadRelations();
});

async function initSelects() 
{
    try 
    {
        // Cargar estudiantes
        const students = await studentsAPI.fetchAll();
        const studentSelect = document.getElementById('studentIdSelect');
        students.forEach(s => 
        {
            const option = document.createElement('option');
            option.value = s.id;
            option.textContent = s.fullname;
            studentSelect.appendChild(option);
        });

        // Cargar materias
        const courses = await coursesAPI.fetchAll();
        const courseSelect = document.getElementById('courseIdSelect');
        courses.forEach(sub => 
        {
            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.name;
            courseSelect.appendChild(option);
        });
    } 
    catch (err) 
    {
        console.error('Error cargando estudiantes o materias:', err.message);
    }
}

function setupFormHandler() 
{
    const form = document.getElementById('relationForm');
    form.addEventListener('submit', async e => 
    {
        e.preventDefault();

        const relation = getFormData();

        try 
        {
            if (relation.id) 
            {
                await studentsCoursesAPI.update(relation);
            } 
            else 
            {
                await studentsCoursesAPI.create(relation);
            }
            clearForm();
            loadRelations();
        } 
        catch (err) 
        {
            console.error('Error guardando relación:', err.message);
        }
    });
}

function getFormData() 
{
    return{
        id: document.getElementById('relationId').value.trim(),
        student_id: document.getElementById('studentIdSelect').value,
        course_id: document.getElementById('courseIdSelect').value,
        passed: document.getElementById('passed').checked ? 1 : 0
    };
}

function clearForm() 
{
    document.getElementById('relationForm').reset();
    document.getElementById('relationId').value = '';
}

async function loadRelations() 
{
    try 
    {
        const relations = await studentsCoursesAPI.fetchAll();
        
        /**
         * DEBUG
         */
        //console.log(relations);

        /**
         * En JavaScript: Cualquier string que no esté vacío ("") es considerado truthy.
         * Entonces "0" (que es el valor que llega desde el backend) es truthy,
         * ¡aunque conceptualmente sea falso! por eso: 
         * Se necesita convertir ese string "0" a un número real 
         * o asegurarte de comparar el valor exactamente. 
         * Con el siguiente código se convierten todos los string 'passed' a enteros.
         */
        relations.forEach(rel => 
        {
            rel.passed = Number(rel.passed);
        });
        
        renderRelationsTable(relations);
    } 
    catch (err) 
    {
        console.error('Error cargando inscripciones:', err.message);
    }
}

function renderRelationsTable(relations) 
{
    const tbody = document.getElementById('relationTableBody');
    tbody.replaceChildren();

    relations.forEach(rel => 
    {
        const tr = document.createElement('tr');

        // tr.appendChild(createCell(rel.fullname || rel.student_id));old
        tr.appendChild(createCell(rel.student_fullname));
        // tr.appendChild(createCell(rel.name || rel.course_id));old
        tr.appendChild(createCell(rel.course_name));
        tr.appendChild(createCell(rel.passed ? 'Sí' : 'No'));
        tr.appendChild(createActionsCell(rel));

        tbody.appendChild(tr);
    });
}

function createCell(text) 
{
    const td = document.createElement('td');
    td.textContent = text;
    return td;
}

function createActionsCell(relation) 
{
    const td = document.createElement('td');

    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.className = 'w3-button w3-blue w3-small';
    editBtn.addEventListener('click', () => fillForm(relation));

    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.className = 'w3-button w3-red w3-small w3-margin-left';
    deleteBtn.addEventListener('click', () => confirmDelete(relation.id));

    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
}

function fillForm(relation) 
{
    document.getElementById('relationId').value = relation.id;
    document.getElementById('studentIdSelect').value = relation.student_id;
    document.getElementById('courseIdSelect').value = relation.course_id;
    document.getElementById('passed').checked = !!relation.passed;
}

async function confirmDelete(id) 
{
    if (!confirm('¿Estás seguro que deseas borrar esta inscripción?')) return;

    try 
    {
        await studentsCoursesAPI.remove(id);
        loadRelations();
    } 
    catch (err) 
    {
        console.error('Error al borrar inscripción:', err.message);
    }
}
