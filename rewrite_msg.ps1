$m = [Console]::In.ReadToEnd()
$m = $m -replace 'Restaurar', 'Restore'
$m = $m -replace 'Adicionar documentação do projeto', 'Add project documentation'
$m = $m -replace 'Adicionar', 'Add'
$m = $m -replace 'Remover', 'Remove'
$m = $m -replace 'Documentação', 'Documentation'
$m = $m -replace 'documentação', 'documentation'
[Console]::Out.Write($m)
