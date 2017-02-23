@echo Inicio del Backup ...
set FECHA=%date%
set FECHA=%FECHA:/=%
set FECHA=%FECHA: =%
set FECHA=%FECHA::=%
set FECHA=%FECHA:,=%
set FILE=C:\\Appserv\\Dropbox\\www\\unohospital\\db\\copias\\Backup_%FECHA%.sql
C:/Appserv/mysql/bin/mysqldump.exe --ignore-table=pev.v_asistencia --ignore-table=pev.v_menu_usuario --routines=TRUE --complete-insert --opt -c -q -f -R -h LOCALHOST -uroot -p123 -r %FILE% unohospital
COPY G:\ARCHIVO DE HISTORIAS CLINICAS\*.* D:\ARCHIVO DE HISTORIAS CLINICAS\
@echo Base de Datos Copiada Exitosamente! ...