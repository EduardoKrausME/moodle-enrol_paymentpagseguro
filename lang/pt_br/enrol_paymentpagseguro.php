<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin lang file.
 *
 * @package    enrol_paymentpagseguro
 * @copyright  2018 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Inscrição com pagamento pelo Pagseguro';
$string['pluginname_desc'] = 'Libera curso mediante pagamento via Pagseguro!';

$string['paymentpagseguro:config'] = 'Configure o métido de inscrição do pagseguro';
$string['paymentpagseguro:manage'] = 'Gerencie os usuários inscritos';
$string['paymentpagseguro:unenrol'] = 'Descadastrar os usuários do curso';
$string['paymentpagseguro:unenrolself'] = 'Retire-me do curso';
$string['unenrolselfconfirm'] = 'Você realmente deseja se desinstalar do curso "{$a}"?';

$string['urlretorno'] = 'A URL de retorno é <code>{$a}</code>!';

$string['email'] = 'Email do PagSeguro';
$string['email_desc'] = 'Email do PagSeguro';

$string['token'] = 'Token do PagSeguro';
$string['token_desc'] = 'Token do PagSeguro';

$string['subscriptions'] = 'Habilitar assinaturas no formulário';
$string['subscriptions_desc'] = 'Se marcado, na inscrição habilita a opção de assinaturas.';

$string['status'] = 'Habilitar assinaturas?';

$string['expiredaction'] = 'Ação ao expirar a matrícula';
$string['expiredaction_help'] = 'Qual ação tomar ao expirar a matrícula?';

$string['cost'] = 'Preço cobrado todo mês';
$string['cost_help'] = 'Valor cobrado em cada mês na mensalidade!';
$string['costerror'] = 'O preço de inscrição deve ser número';

$string['cost2'] = 'Preço para inscrição';
$string['cost2_help'] = 'Valor que o usuário deve pagar para ter acesso ao curso!';

$string['months'] = 'Número de meses';
$string['months_help'] = 'Se definir como 0 (ZERO) o pagamento é único. Se definir maior que 3 será mensalidade!';
$string['monthserror'] = 'Mês deve ser um número inteiro entre 0 e 24!';
$string['monthsmaxerror'] = 'Máximo 24 meses';

$string['faulback'] = 'Desativar na inadimplência?';
$string['faulback_help'] = 'Se o pagamento da mensalidade falhar, a matrícula deve ser desativada?<br>Em caso de pagamento único remove caso de Chargeback ou disputa.';

$string['enrolperiod'] = 'Duração da inscrição';
$string['enrolperiod_help'] = 'Duração de tempo que a inscrição é válida, iniciando no momento que o usuário é inscrito. Caso desabilitado, a duração da inscrição será ilimitada. Este tempo não tem efeito em Assinaturas!';

$string['enrolstartdate'] = 'Início das inscrições';
$string['enrolstartdate_help'] = 'Se habilitado, os usuários só podem ser inscritos a partir desta data.';

$string['enrolenddate'] = 'Data limite das inscrições';
$string['enrolenddate_help'] = 'Se habilitado, os usuários só podem se inscrever até esta data.';

$string['defaultrole'] = 'Atribuir papel';
$string['defaultrole_help'] = 'Selecione o papel que deve ser atribuído aos usuários durante as inscrições pagas via Pagseguro';

$string['requestpayforpagseguro'] = 'Este curso requer o pagamento da taxa de inscrição antes do acesso.';
$string['payforpagseguro'] = 'Pagar agora com PagSeguro';
$string['paytext'] = 'Todo dia {$a->date} será cobrado o valor de R${$a->costlocaled} referente ao curso {$a->fullname}';

$string['costunique'] = 'Custo: R${$a}';
$string['costmonthly'] = 'Custo mensal: R${$a}';

$string['errornoenrolment'] = 'Nenhuma matrícula localizada!';
$string['errorlowvalue']='Valor é muito baixo!';
$string['errorapi']='API PagSeguro!';