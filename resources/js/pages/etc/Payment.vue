<template>
    <div class="card container">
        <div class="card-header">
            <h3>Select a payment method</h3>
        </div>
        <div class="container card-body row m-0 p-5">
            <div class="col-md-4 text-center mx-auto"><b-img @click="chooseMethod('bKash')" thumbnail fluid src="https://i.pinimg.com/564x/ca/cf/06/cacf069ae0c936382a7e6e7a46373773.jpg" alt="bKash Payment" style="cursor: pointer;"></b-img></div>
            <div class="col-md-4 text-center mx-auto"><b-img @click="chooseMethod('Nexus')" thumbnail fluid src="https://i.pinimg.com/originals/f8/92/9b/f8929b9d4009d70ff1eb5258a9880ce2.png" alt="Nexus Payment" style="cursor: pointer;"></b-img></div>
            <div class="col-md-4 text-center mx-auto"><b-img @click="chooseMethod('SEBL')" thumbnail fluid src="https://i.pinimg.com/originals/54/57/0e/54570ebdcbbd9ee186c1b1834b8a8e3e.jpg" alt="SEBL Payment" style="cursor: pointer; width: 100%;"></b-img></div>
        </div>
        <!-- Start Payment Details Modal -->
        <b-modal ref="payment" id="payment" size="lg" title="Scan the QR code and pay ৳ 3,000" no-close-on-backdrop ok-disabled>                        
            <div class="container card-body row m-0 p-5">
                <div v-if="paymentMethod == 'bKash'" class="col-md-6 text-center mx-auto"><b-img fluid src="https://i.pinimg.com/originals/80/5a/a4/805aa4838b96c86c86d0f591591c71bd.jpg" alt="bKash Payment" style="cursor: pointer;"></b-img></div>
                <div v-if="paymentMethod == 'Nexus'" class="col-md-6 text-center mx-auto"><b-img fluid src="https://i.pinimg.com/originals/3a/c1/19/3ac11952f81cf1daa8a48655591178f5.jpg" alt="Nexus Payment" style="cursor: pointer;"></b-img></div>
                <div v-if="paymentMethod == 'SEBL'" class="col-md-6 text-center mx-auto"><b-img fluid src="https://i.pinimg.com/originals/60/61/62/606162661fff55faee91d9560dc6d9f6.jpg" alt="Nexus Payment" style="cursor: pointer;"></b-img></div>
            </div>
            <template v-slot:modal-footer="">
                <button @click="$refs['payment'].hide()" type="button" class="mdb btn btn-outline-mdb-color">{{$t('Close')}}</button>
            </template>
        </b-modal>                    
        <!-- End Payment Details Modal -->
    </div>
</template>
<script>
export default {
    data() {
        return{
            paymentMethod: null,
        }
    },

    methods: {
        chooseMethod(method) {
            this.paymentMethod = method
            this.$refs['payment'].show()
        },
    }
}
</script>