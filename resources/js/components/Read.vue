<template>
  <div class="container" style="margin: auto">
    <div v-if="modelname.length > 0">
      <p>
        <b>{{ modelname }}</b>
      </p>
      <table class="table table-bordered table-dark">
        <thead>
          <tr>
            <td v-for="elem in heads">{{ elem }}</td>
            <td>usuń</td>
            <td>edytuj</td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="elem in dane">
            <td v-for="head in heads">{{ elem[head] }}</td>
            <td>
              <button @click="mydestroy(elem.id)" class="btn btn-sm btn-danger">
                Usuń
              </button>
            </td>
            <td>
              <button @click="edit(elem.id)" class="btn btn-sm btn-danger">
                Edytuj
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-else>Zapodaj nazwę modelu by użyć komponentu Read</div>

    <div id="edit">
      <div>
        <p v-if="mode == 'edit'">
          <b>Edytuj rekord</b>
        </p>
        <p v-else>
          <b>Stwórz nowy rekord</b>
        </p>

        <div>
          <!-- <input type="hidden" class="form-control" :value="cruddata.id" name="id"> -->

          <!-- TU WSTAW HTML -->
          <label>Nazwa</label>
          <input v-model="cruddata.title" />
          <label>Opis</label>
          <input v-model="cruddata.description" />
          <label>Czas</label>
          <input type="date" v-model="cruddata.date" />

              <label>Czas</label>
          <input type="time" v-model="cruddata.time" />

          <!-- koniec html -->
          <button type="button" @click="add" v-if="mode == 'create'">
            zapisz
          </button>
          <button type="button" @click="update" v-if="mode == 'edit'">
            Zmień
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { EventBus } from "../event-bus.js";

export default {
  props: ["modelname"],
  data() {
    return {
      dane: [],
      hidden: ["created_at", "updated_at", "category_id"],
      mode: "create",
      editid: null,
      cruddata: {},
    };
  },
  methods: {
    getData() {
      let self = this;
      //axios.get("/category").then(res => console.log(res));
      axios
        .get("/" + self.modelname.toLowerCase())
        .then((res) => (self.dane = res.data));
    },
    mydestroy(id) {
      let self = this;
      axios
        .delete("/" + self.modelname.toLowerCase() + "/" + id)
        .then((res) => self.getData());
    },
    edit(id) {
      this.editid = id;
      this.mode = "edit";
      let freshobject = {};
      freshobject = Object.create(this.dane.find((el) => el.id == id));
      this.cruddata = freshobject;
    },
    update() {
      let self = this;
      axios
        .patch(
          "/" + self.modelname.toLowerCase() + "/" + self.editid,
          this.cruddata
        )
        .then((res) => self.getData());
    },
    add() {
      let self = this;
      axios
        .post("/" + this.modelname.toLowerCase(), this.cruddata)
        .then((res) => self.getData());
    },
    reset() {
      this.cruddata = {};
    },
  },
  mounted() {
    this.getData();
    EventBus.$on("reload", (payload) => {
      console.log("działa");
      this.getData();
    });
  },
  computed: {
    heads() {
      let self = this;
      // return typeof(this.dane[0])
      if (typeof this.dane[0] == "object") {
        return Object.keys(this.dane[0]).filter(
          (el) => self.hidden.indexOf(el) == -1
        );
      } else {
        return [];
      }
    },
  },
};
</script>