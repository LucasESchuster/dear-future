# - PT-BR
## Introdução

Esse projeto tem como objetivo funcionar como uma cápsula do tempo. O usuário escreve uma mensagem e seleciona uma data para ser notificado. 

## Objetivos do projeto

### Endpoints:
- Cadastro de usuário. 
- Login de usuário.
- Cadastro de mensagem.
- Cancelamento de envio da mensagem.
- Listar a data de quando as mensagens serão enviadas.

### Observações:
- O usuário não poderá ver o conteúdo do cadastro da mensagem após cadastrada.
- Todos os endpoints deverão ser seguros, com middlewares que verifiquem se o usuário pode ou não tomar determinada ação.
- A mensagem poderá ser enviada para uma ou mais pessoas. 

## Tecnologias utilizadas
- API: Laravel
- Banco de dados: PostgreSQL e Redis
- Jobs: Laravel Jobs