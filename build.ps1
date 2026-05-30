# Builds the distributable plugin zip with correct (forward-slash) ZIP entry
# paths. Windows PowerShell 5.1's Compress-Archive writes backslash separators,
# which breaks extraction on WordPress.org / Linux. This script avoids that.
#
# Usage:  powershell -File build.ps1 -Version 1.4

param(
    [string]$Version = "1.4"
)

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$slug    = "mirket-popup-redirect-countdown"
$root    = Split-Path -Parent $MyInvocation.MyCommand.Path
$zipPath = Join-Path $root "$slug-v$Version.zip"
if (Test-Path $zipPath) { Remove-Item -Force $zipPath }

# Only ship runtime files. README.md, docs, build script and old zips are excluded.
$map = [ordered]@{
    "mirket-popup-redirect-countdown.php"    = "$slug/mirket-popup-redirect-countdown.php"
    "readme.txt"                             = "$slug/readme.txt"
    "LICENSE"                                = "$slug/LICENSE"
    "includes\class-mirketprc-admin.php"     = "$slug/includes/class-mirketprc-admin.php"
    "includes\class-mirketprc-frontend.php"  = "$slug/includes/class-mirketprc-frontend.php"
    "assets\css\mirketprc-frontend.css"      = "$slug/assets/css/mirketprc-frontend.css"
    "assets\css\mirketprc-admin.css"         = "$slug/assets/css/mirketprc-admin.css"
    "assets\js\mirketprc-frontend.js"        = "$slug/assets/js/mirketprc-frontend.js"
    "assets\js\mirketprc-admin.js"           = "$slug/assets/js/mirketprc-admin.js"
}

$zip = [System.IO.Compression.ZipFile]::Open($zipPath, [System.IO.Compression.ZipArchiveMode]::Create)
foreach ($disk in $map.Keys) {
    $full = Join-Path $root $disk
    [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
        $zip, $full, $map[$disk], [System.IO.Compression.CompressionLevel]::Optimal) | Out-Null
}
$zip.Dispose()

Write-Output "Built: $zipPath"
