import { createAPI } from './apiFactory.js'; // importa el generador de APIs
export const studentsCoursesAPI = createAPI('studentsCourses'); // crea la API de estudiantes-materias

// la api puede extenderse, por ejemplo, agregando un nuevo método
// esto puede lograrse utilizando los siguientes fragmentos

// ...studentsCoursesAPI - hereda las funciones ya existentes
// async fetchByStudentId(id) - crea un método personalizado para obtener un estudiante a trvés de una id
// const res = await fetch(`../../backend/server.php?module=studentsCourses&studentId=${id}`); - hace la nueva consulta

// además, mediante la propiedad 'urlOverride' dentro del 'config' anónimo definido dentro del generador, puede personalizarse la URL como sea necesario

// const customAPI = createAPI('custom', {urlOverride: '../../backend/misRutas/personalizadas.php'});
