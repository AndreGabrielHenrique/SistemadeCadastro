param([string]$file)
if (-not $file) { exit 1 }
$m = Get-Content -Raw -Path $file -ErrorAction Stop
$m = $m -replace 'Restaurar', 'Restore'
$m = $m -replace 'Adicionar documentação do projeto', 'Add project documentation'
$m = $m -replace 'Adicionar', 'Add'
$m = $m -replace 'Remover', 'Remove'
$m = $m -replace 'Documentação', 'Documentation'
$m = $m -replace 'documentação', 'documentation'
Set-Content -NoNewline -Encoding utf8 -Path $file -Value $m
