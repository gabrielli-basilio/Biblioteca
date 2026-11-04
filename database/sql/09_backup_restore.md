# Backup e restauração do banco
## 1. Criando cópia SQL (texto)

```bash
pg_dump 'postgresql://neondb_owner:npg_bCXRJ54rKVTQ@ep-curly-poetry-b6alpnbk-pooler.c-2.sa-east-1.aws.neon.tech/Bribrioteca?sslmode=require&channel_binding=require' -n bribriotecaDB -F p -f database/backups/backup_gabrielli.sql
```

## 2. Criando cópia Dump (formato custom)

```bash
pg_dump 'postgresql://neondb_owner:npg_bCXRJ54rKVTQ@ep-curly-poetry-b6alpnbk-pooler.c-2.sa-east-1.aws.neon.tech/Bribrioteca?sslmode=require&channel_binding=require' -n bribriotecaDB -F c -f database/backups/backup_gabrielli.dump
```

## 3. Restaurando

```bash
pg_restore -d 'postgresql://neondb_owner:npg_bCXRJ54rKVTQ@ep-curly-poetry-b6alpnbk-pooler.c-2.sa-east-1.aws.neon.tech/bribrioteca_restore?sslmode=require&channel_binding=require' --no-owner --no-acl database/backups/backup_gabrielli.dump
```