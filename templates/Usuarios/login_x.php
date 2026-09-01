<?= $this->Html->script(['jquery.maskedinput', 'jquery.mask']); ?>
<div class="container d-flex justify-content-center position-relative mt-5 top-100 start-0">
    <div class="col-4 position-absolute mt-4 p-2 border border-info rounded-4 bg-azul">
        <span class="text-white d-flex justify-content-center">LOGIN</span>
    </div>
    <div class="col-12 mt-5 d-flex justify-content-center">
        <div class="col-6 p-4 border border-info rounded-5">
            <?= $this->Form->create(null, ['autocomplete' => 'off']) ?>

            <div class="col-12 border border-info rounded-3 bg-azul d-flex align-items-center my-2">
                <div class="col-2 d-flex justify-content-center ">
                    <span class="text-white">Login</span>
                </div>

                <div class="col-10 border border-info rounded-3">
                    <?= $this->Form->control('cd_cpf', ['id' => 'login-input', 'class' => 'cpf', 'label' => false]) ?>
                    <?= $this->Form->control('cd_rf', ['type' => 'hidden', 'label' => false]) ?>
                </div>
            </div>

            <div class="col-12 border border-info rounded-3 bg-azul d-flex align-items-center my-2">

                <div class="col-2 d-flex justify-content-center ">
                    <span class="text-white">Senha</span>
                </div>

                <div class="col-10 border border-info rounded-3">
                    <?= $this->Form->control('password', ['label' => false]) ?>
                </div>

            </div>
            <div class="col-12 d-flex justify-content-center mt-5">
                <div>
                    <button class="btn btn-verdeM rounded-5 text-white px-5">ENTRAR</button>
                </div>
            </div>
            <?= $this->Form->end() ?>

        </div>
    </div>
</div>
<script>
    document.getElementById('login-input').addEventListener('focus', function() {
        this.value = '';
    });
    document.getElementById('password').addEventListener('focus', function() {
        this.value = '';
    });
    
    const input = document.getElementById('login-input');
    const cpfrf = document.getElementById('cd-rf');

    // alert (input);
    input.addEventListener('keyup', function() {
        cpfrf.value = input.value.replace(/[^0-9]/g, "");

    })

    document.getElementById("login-input").addEventListener("blur", function() {
        let valor = this.value.replace(/\D/g, ""); // só números

        if (valor.length === 11) {
            if (!validaCPF(valor)) {
                // alert("CPF inválido!");
                this.focus();
            }
        } else if (valor.length >= 5) {
            // aqui você trata como Registro Funcional
            console.log("Registro funcional detectado:", valor);
        } else if (valor.length >= 11) {
            console.log("CPF detectado:", valor);
        // } else {
        //     alert("Digite um CPF válido (11 dígitos) ou um Registro funcional.");
        //     this.focus();
        }
    });

    function validaCPF(cpf) {
        let soma = 0;
        let resto;

        if (cpf == "00000000000") return false;

        for (let i = 1; i <= 9; i++)
            soma = soma + parseInt(cpf.substring(i - 1, i)) * (11 - i);
        resto = (soma * 10) % 11;

        if (resto == 10 || resto == 11) resto = 0;
        if (resto != parseInt(cpf.substring(9, 10))) return false;

        soma = 0;
        for (let i = 1; i <= 10; i++)
            soma = soma + parseInt(cpf.substring(i - 1, i)) * (12 - i);
        resto = (soma * 10) % 11;

        if (resto == 10 || resto == 11) resto = 0;
        if (resto != parseInt(cpf.substring(10, 11))) return false;

        return true;
    }
</script>