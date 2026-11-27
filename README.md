# PIMA-VS2ASIR — Rutoletum

Repositorio del proyecto intermodular de 2.º de ASIR. Rutoletum es una aplicación de rutas turísticas de Toledo que utiliza WordPress y OSRM.

## 1. Guía rápida para el equipo

| Archivo | Cuándo ejecutarlo | Qué hace |
| --- | --- | --- |
| `Configurar-Git.bat` | Solo la primera vez | Configura la identidad de Git, inicializa LFS y prepara tu rama personal. |
| `Actualizar-Proyecto.bat` | Antes de trabajar | Integra las novedades de tu rama remota y de `main` y descarga los objetos LFS necesarios. |
| `Subir-Cambios.bat` | Al terminar de trabajar | Prepara los cambios, crea un commit y lo sube a tu rama personal. |
| `Git-Equipo.ps1` | No se ejecuta directamente en el uso normal | Contiene la lógica que llaman los tres archivos BAT. |

## 2. Primera instalación (Windows)

1. Instala [Git para Windows](https://git-scm.com/download/win). Conserva Git LFS y Git Credential Manager en la instalación.
2. Acepta la invitación al repositorio privado desde tu propia cuenta de GitHub.
3. Abre PowerShell en la carpeta donde quieras guardar el proyecto y ejecuta:

   ```powershell
   git --version
   git lfs version
   git lfs install
   git clone https://github.com/PIMA-VS2ASIR/PIMA-VS2ASIR.git
   cd PIMA-VS2ASIR
   ```

   Si Git Credential Manager solicita autenticación, inicia sesión con tu cuenta de GitHub. No compartas contraseñas ni tokens.

4. Abre la carpeta clonada y haz doble clic en `Configurar-Git.bat`, o ejecuta:

   ```powershell
   .\Configurar-Git.bat
   ```

5. Introduce tu usuario de GitHub (sin `@`), tu nombre y un correo asociado a tu cuenta. Puedes utilizar el correo `noreply` que aparece en GitHub > Settings > Emails.
6. Comprueba que aparece `Configuracion terminada. Tu rama: equipo/tuusuario`.

La primera descarga puede tardar debido a los datos de OSRM. Necesitas espacio disponible y acceso a los objetos de Git LFS. No utilices **Download ZIP** para sustituir el clonado: los scripts necesitan el repositorio Git local.

Si ya tienes un clon, no lo borres. Guarda primero tus cambios y consulta con el responsable si tienes trabajo pendiente o todavía no tienes los scripts actualizados.

## 3. Rutina diaria

### Antes de trabajar

1. Ejecuta `Actualizar-Proyecto.bat` desde la raíz del repositorio.
2. Comprueba que termina correctamente. Si hay cambios pendientes, el script se detendrá: no los borres.
3. Modifica los archivos necesarios de la aplicación.

### Al terminar

1. Guarda todos los archivos en el editor.
2. Ejecuta `Subir-Cambios.bat`.
3. Revisa la lista de archivos y confirma con `S` si es correcta. Se incluyen todos los cambios no ignorados, también las eliminaciones.
4. Escribe un mensaje descriptivo, por ejemplo: `Mejorado el filtro de rutas`.
5. Confirma nuevamente con `S`. Si se solicita autenticación, inicia sesión.
6. Comprueba que aparece `Subida correcta a equipo/tuusuario`.

Si cancelas después de preparar los archivos, estos quedan preparados localmente. Si falla la subida después de crear un commit, el commit sigue guardado en tu equipo.

### Incorporar los cambios a main

1. Abre el [repositorio en GitHub](https://github.com/PIMA-VS2ASIR/PIMA-VS2ASIR).
2. En Pull requests, pulsa New pull request (o Compare & pull request si aparece).
3. Selecciona `main` como rama base y `equipo/tuusuario` como rama de comparación.
4. Revisa **Files changed**, escribe un título y una descripción y crea el Pull Request.
5. Otro compañero con permisos debe revisar y aprobar los cambios. No apruebes tu propio Pull Request.
6. Una vez resueltos los comentarios y conflictos, integra el Pull Request cuando GitHub lo permita.

Las ramas `equipo/usuario` se reutilizan. Para este flujo recomendamos **Create a merge commit**, si está habilitado, y no borrar la rama personal tras la fusión. Así `main` conserva la ascendencia de los commits de la rama y la siguiente actualización resulta más sencilla.

## 4. Organización del repositorio

```text
PIMA-VS2ASIR/
├── .gitattributes
├── .gitignore
├── Configurar-Git.bat
├── Actualizar-Proyecto.bat
├── Subir-Cambios.bat
├── Git-Equipo.ps1
├── README.md
├── toledo-rutas/
│   ├── .gitattributes
│   ├── .gitignore
│   ├── osrm-data/
│   └── wordpress/
├── v0.1a/
└── v0.2a/
```

Se muestran los archivos y directorios relevantes, no todo el contenido del repositorio.

- `main`: versión oficial del proyecto.
- `equipo/usuario`: rama personal de cada colaborador.
- `toledo-rutas/`: aplicación actual.
- `v0.1a/` y `v0.2a/`: versiones anteriores; no modificarlas sin acordarlo con el equipo.

## 5. Git LFS

Las reglas existentes del repositorio son:

En `.gitattributes`:

```gitattributes
*.osm.pbf filter=lfs diff=lfs merge=lfs -text
```

En `toledo-rutas/.gitattributes`:

```gitattributes
osrm-data/* filter=lfs diff=lfs merge=lfs -text
```

Git LFS almacena los objetos gestionados por estas reglas fuera de los blobs ordinarios de Git; el historial de Git contiene punteros con el hash SHA-256 y el tamaño del objeto. La segunda regla cubre los archivos directamente dentro de `osrm-data/`, no sus posibles subdirectorios.

No elimines estas reglas ni regeneres los datos de OSRM sin coordinarte con el equipo. LFS no gestiona automáticamente todos los archivos pesados del repositorio: para otros formatos hay que definir reglas adecuadas. El almacenamiento y la transferencia de LFS están sujetos a los límites de la cuenta.

Comandos de diagnóstico (no necesarios en el uso diario):

```powershell
git lfs track
git lfs ls-files
git lfs fsck --objects HEAD
git lfs fsck --pointers HEAD
```

## 6. Seguridad y buenas prácticas

- Utiliza tu propia cuenta y rama. No hagas push directamente a `main`.
- Revisa los archivos antes de confirmar; no subas contraseñas, tokens, claves privadas, archivos `.env` ni datos personales innecesarios.
- Respeta `.gitignore` y `.gitattributes`. `.gitignore` no elimina del control de versiones los archivos que ya estaban registrados.
- Evita trabajar simultáneamente en la misma rama desde varios equipos o modificar los mismos archivos que otro compañero sin coordinarte.
- No utilices `git push --force`, `git reset --hard` o comandos que descarten cambios para resolver errores.
- Ejecuta los scripts únicamente desde el repositorio de confianza. Los BAT llaman a PowerShell con `-ExecutionPolicy Bypass` para ese proceso; no cambian la política global de Windows. Revisa el contenido si se modifica.

## 7. Errores frecuentes

| Mensaje o problema | Qué hacer |
| --- | --- |
| Git no se reconoce | Instala Git para Windows y abre otra terminal. |
| Git LFS no está disponible | Comprueba que Git LFS está instalado. Ejecuta `git lfs version`. |
| Error de autenticación o repositorio no encontrado | Comprueba la invitación y la cuenta con la que has iniciado sesión. |
| No estás en tu rama configurada | Comprueba `git branch --show-current`. No cambies de rama con trabajo pendiente sin ayuda. |
| Hay cambios locales al actualizar | Guarda tu trabajo y súbelo mediante el script antes de actualizar, si es adecuado. |
| Conflicto al integrar main | El script intenta cancelar la fusión. Pide ayuda al responsable antes de continuar. |
| La rama ha divergido / fast-forward no posible | No fuerces el push. Pide ayuda para integrar los commits locales y remotos. |
| Fallo de LFS | Revisa la conexión, el espacio, los permisos y los límites de LFS. Conserva los archivos originales. |
| El Pull Request no permite fusionar | Comprueba aprobaciones, conversaciones pendientes, conflictos y reglas de `main`. |

Para pedir ayuda, envía el mensaje de error y la salida de:

```powershell
git status
git branch -vv
git log -1 --oneline
```

Oculta credenciales y datos privados antes de compartir capturas.

## 8. Funcionamiento técnico

- **Configurar:** verifica Git, LFS y el repositorio; ejecuta `git lfs install --local`, obtiene referencias remotas, crea o recupera `equipo/usuario`, sincroniza sin reescribir historial, descarga LFS y guarda la identidad y `equipo.rama` en la configuración local.
- **Actualizar:** exige una rama personal configurada y un directorio de trabajo limpio; ejecuta `git fetch --prune origin`, integra la rama remota personal con `git merge --ff-only` si existe, incorpora `origin/main` con `git merge --no-edit`, descarga LFS y comprueba los punteros. No hace push.
- **Subir:** comprueba la rama configurada, solicita confirmación, prepara los archivos con `git add -A`, crea un commit si hay cambios preparados, sincroniza la rama personal remota sin reescribir el historial, comprueba punteros LFS y ejecuta `git push -u origin` hacia la rama personal. Si no hay cambios nuevos, también permite subir commits pendientes.
- **Protección de main:** el ruleset `proteccion-main` se ha configurado para exigir Pull Requests con revisión y bloquear force pushes y eliminaciones. Su aplicación efectiva depende del plan y del estado de las reglas de GitHub.
- **Despliegue:** esta automatización prepara el flujo colaborativo. Un futuro workflow de GitHub Actions puede desplegar los cambios aprobados de `main` con Docker y descargar los objetos LFS mediante `lfs: true` en el paso de checkout. Los scripts no despliegan la aplicación por sí mismos.

## 9. Validación

Se han realizado pruebas en Windows con Git 2.56.1 y Git LFS 3.8.1: configuración inicial, creación de rama personal, subida de archivos normales y LFS, eliminación de archivos, comprobaciones locales de integridad y actualización mediante un commit nuevo de `main`. También se solicitó la descarga remota del objeto de prueba sin errores.

Queda pendiente validar específicamente una fusión con contenido nuevo no presente en la rama personal, un conflicto intencionado y la aplicación efectiva del ruleset. El sistema no sustituye la revisión humana ni resuelve automáticamente conflictos de código.
