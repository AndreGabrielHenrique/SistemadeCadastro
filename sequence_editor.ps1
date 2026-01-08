param([string]$file)
if (-not $file) { exit 1 }
$lines = Get-Content -Raw -Path $file -ErrorAction Stop
$out = @()
foreach ($line in $lines -split "`n") {
    $trim = $line.TrimEnd()
    if ($trim -match '^(pick|reword|edit)\s+([0-9a-f]+)\s+(.*)$') {
        $msg = $matches[3]
        if ($msg -match 'Restaurar|Adicionar|Remover|Documentação|documentação') {
            $new = $trim -replace '^pick','reword'
            $out += $new
            continue
        }
    }
    $out += $trim
}
$out -join "`n" | Set-Content -NoNewline -Encoding utf8 $file
