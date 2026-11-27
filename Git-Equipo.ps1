param([Parameter(Mandatory=$true)][ValidateSet('Configurar','Subir')][string]$Modo)
$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath $PSScriptRoot

function Invoke-RepoGit {
    & git @args
    if ($LASTEXITCODE -ne 0) { throw "Git devolvio un error ($LASTEXITCODE). Revisa la salida anterior." }
}
function Existe-Ref([string]$ref) {
    & git show-ref --verify --quiet $ref
    if ($LASTEXITCODE -eq 0) { return $true }
    if ($LASTEXITCODE -eq 1) { return $false }
    throw 'No se pudo comprobar la referencia.'
}
function Comprobar-Operacion {
    foreach ($estado in @('MERGE_HEAD','CHERRY_PICK_HEAD','REVERT_HEAD','rebase-merge','rebase-apply','sequencer')) {
        $ruta = Invoke-RepoGit rev-parse --git-path $estado
        if (Test-Path -LiteralPath $ruta) { throw 'Hay una operacion Git pendiente. Resuelvela antes de continuar.' }
    }
}
try {
    Write-Host "=== RUTOLETUM / $Modo ==="
    if (-not (Get-Command git -ErrorAction SilentlyContinue)) { throw 'Instala Git para Windows y vuelve a abrir este archivo.' }
    Invoke-RepoGit --version
    Invoke-RepoGit lfs version
    $raiz = Invoke-RepoGit rev-parse --show-toplevel
    if ((Resolve-Path -LiteralPath $raiz).Path -ne (Resolve-Path -LiteralPath $PSScriptRoot).Path) { throw 'Coloca estos archivos en la raiz del repositorio clonado.' }
    $null = Invoke-RepoGit remote get-url origin
    Comprobar-Operacion
    if (-not (Test-Path -LiteralPath '.gitattributes')) { throw 'Falta .gitattributes. Pide al administrador que configure LFS.' }
    Invoke-RepoGit lfs install --local

    if ($Modo -eq 'Configurar') {
        if (@(Invoke-RepoGit status --porcelain).Count -ne 0) { throw 'Hay cambios locales. Guarda tus cambios antes de configurar o cambiar de rama.' }
        $usuario = (Read-Host 'Tu nombre de usuario de GitHub (sin @)').Trim().ToLowerInvariant()
        if ($usuario -notmatch '^[a-z0-9][a-z0-9-]{0,38}$') { throw 'Nombre de usuario no valido.' }
        $nombre = (Read-Host 'Tu nombre para los commits').Trim()
        $correo = (Read-Host 'Tu correo de GitHub (puedes usar el noreply de Settings > Emails)').Trim()
        if (-not $nombre -or $correo -notmatch '^[^\s@]+@[^\s@]+\.[^\s@]+$') { throw 'Nombre o correo no valido.' }
        $rama = "equipo/$usuario"
        Invoke-RepoGit fetch origin
        if (Existe-Ref "refs/heads/$rama") {
            Invoke-RepoGit switch $rama
        } elseif (Existe-Ref "refs/remotes/origin/$rama") {
            Invoke-RepoGit switch --track -c $rama "origin/$rama"
        } else {
            Invoke-RepoGit switch --no-track -c $rama origin/main
        }
        if (Existe-Ref "refs/remotes/origin/$rama") {
            Invoke-RepoGit merge --ff-only "origin/$rama"
        }
        Invoke-RepoGit lfs pull origin
        Invoke-RepoGit config --local user.name $nombre
        Invoke-RepoGit config --local user.email $correo
        Invoke-RepoGit config --local equipo.rama $rama
        Write-Host "Configuracion terminada. Tu rama: $rama" -ForegroundColor Green
        exit 0
    }

    $rama = Invoke-RepoGit symbolic-ref --short HEAD
    $esperada = Invoke-RepoGit config --local --get equipo.rama
    if ($rama -ne $esperada -or $rama -notmatch '^equipo/[a-z0-9][a-z0-9-]{0,38}$') { throw 'No estas en tu rama configurada. No se subira nada.' }
    $null = Invoke-RepoGit var GIT_AUTHOR_IDENT
    $null = Invoke-RepoGit var GIT_COMMITTER_IDENT
    Invoke-RepoGit status --short
    if (@(Invoke-RepoGit status --porcelain).Count -ne 0) {
        Write-Host 'Se incluiran todos los cambios no ignorados, tambien eliminaciones.'
        if ((Read-Host 'Preparar todos los cambios? (S/N)') -notmatch '^[sS]$') { Write-Host 'Cancelado.'; exit 0 }
        Invoke-RepoGit add -A
        Invoke-RepoGit diff --cached --stat
        Invoke-RepoGit diff --cached --name-status
        & git diff --cached --quiet
        $codigo = $LASTEXITCODE
        if ($codigo -eq 1) {
            $mensaje = (Read-Host 'Describe el cambio (Enter para mensaje automatico)').Trim()
            if (-not $mensaje) { $mensaje = 'Actualizacion - ' + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss') }
            if ((Read-Host 'Crear commit y subir? (S/N)') -notmatch '^[sS]$') { Write-Host 'Cancelado. Los cambios quedan preparados localmente.'; exit 0 }
            Invoke-RepoGit commit -m $mensaje
        } elseif ($codigo -ne 0) { throw 'No se pudo comprobar el contenido del commit.' }
    } else {
        Write-Host 'No hay cambios nuevos. Se comprobaran los commits locales pendientes.'
        if ((Read-Host 'Sincronizar y subir tu rama? (S/N)') -notmatch '^[sS]$') { Write-Host 'Cancelado.'; exit 0 }
    }
    Invoke-RepoGit fetch origin
    if (Existe-Ref "refs/remotes/origin/$rama") {
        Invoke-RepoGit merge --ff-only "origin/$rama"
    }
    Invoke-RepoGit lfs fsck --pointers HEAD
    Invoke-RepoGit push -u origin "HEAD:refs/heads/$rama"
    Write-Host "Subida correcta a $rama. Revisa o crea tu Pull Request en GitHub." -ForegroundColor Green
    exit 0
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host 'No se fuerza ninguna subida. Si ya se creo un commit, sigue guardado localmente.'
    exit 1
}
