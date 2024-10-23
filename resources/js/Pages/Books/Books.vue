<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";
import { useToast } from "vue-toastification";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const toast = useToast();

const books = ref([]);
const sortOrder = ref("asc");
const searchTerm = ref("");
const showModal = ref(false);
const coverFile = ref(null);
const changeCover = ref(false);

const currentBook = ref({
  name: "",
  author: "",
  cover: null,
});

const showCover = () => {
  changeCover.value = true;
};

const closeCover = () => {
  changeCover.value = false;
};

const fetchBooks = async (order = "asc") => {
  try {
    const response = await axios.get("/books", {
      params: {
        sort: order,
        search: searchTerm.value,
      },
    });
    books.value = response.data;
  } catch (error) {}
};

const openEditModal = (book) => {
  currentBook.value = { ...book };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  currentBook.value = null;
  changeCover.value = false;
};

const handleFileChange = (event) => {
  coverFile.value = event.target.files[0];
};

const updateBook = async () => {
  const formData = new FormData();

  formData.append("name", currentBook.value.name);
  formData.append("author", currentBook.value.author);

  if (coverFile.value) {
    formData.append("cover", coverFile.value);
  }

  console.log(formData.value);
  try {
    await axios.post(`/books-update/${currentBook.value.id}`, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
    fetchBooks();
    closeModal();
    toast.success("Book Edited Successfully");
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const messages = error.response.data.errors;
      for (const key in messages) {
        messages[key].forEach((msg) => {
          toast.error(msg);
        });
      }
    } else {
      toast.error("Failed to Edit Book");
    }
  }
};

const deleteBook = async () => {
  try {
    await axios.delete(`/books/${currentBook.value.id}`);

    fetchBooks();
    closeModal();
    toast.success("Book Deleted Successfully");
  } catch (error) {
    toast.error("Failed to Delete Book");
  }
};
const sortBooks = (order) => {
  sortOrder.value = order;
  fetchBooks(order);
};

const toggleSortOrder = () => {
  sortOrder.value = sortOrder.value === "asc" ? "dec" : "asc";
};
watch([searchTerm, sortOrder], () => {
  fetchBooks();
});
onMounted(() => {
  fetchBooks();
});
</script>

<style lang="scss" scoped></style>

<template>
  <Head title="Books" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Books</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div
          class="overflow-hidden bg-white shadow-sm sm:rounded-lg text-sm grid-cols-1 justify-center items-center"
        >
          <div class="flex space-x-2 mt-2 p-2">
            <input
              type="text"
              v-model="searchTerm"
              class="text-xs"
              placeholder="Search by Book Title or Author Name"
            />
            <button
              @click="sortBooks('asc')"
              class="bg-gray-100 px-2 py-1"
              :class="{
                'font-bold text-white bg-gray-800 rounded-sm ': sortOrder === 'asc',
              }"
            >
              <i class="fa-solid fa-arrow-up"></i> A-Z
            </button>
            <button
              @click="sortBooks('desc')"
              class="bg-gray-100 px-2 py-1"
              :class="{
                'font-bold text-white bg-gray-800 ': sortOrder === 'desc',
              }"
            >
              <i class="fa-solid fa-arrow-down"></i> Z-A
            </button>

            <button class="px-2 py-1 bg-gray-800 text-gray-50 rounded-sm">
              Import CSV
            </button>
            <button class="px-2 py-1 bg-gray-800 text-gray-50 rounded-sm">
              Export CSV
            </button>
          </div>

          <table class="mt-4 p-2">
            <thead>
              <tr>
                <th class="py-1 px-2 border-b">Cover</th>
                <th class="py-1 px-4 border-b">Book Title</th>
                <th class="py-1 px-4 border-b">Author</th>
                <th class="py-1 px-4 border-b">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="book in books" :key="book.id">
                <td class="py-1 px-2 border-b">
                  <img
                    :src="`/storage/${book.cover}`"
                    alt="Book Cover"
                    class="w-10 h-10 object-cover rounded-md"
                  />
                </td>
                <td class="py-1 px-4 border-b">
                  {{ book.name }}
                </td>
                <td class="py-1 px-4 border-b">
                  {{ book.author }}
                </td>
                <td class="py-1 px-4 border-b">
                  <button
                    @click="openEditModal(book)"
                    class="text-blue-600 font-semibold hover:text-gray-700"
                  >
                    <i class="fa-regular fa-eye"></i> View
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div
      v-if="showModal"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="bg-white p-4 rounded shadow-md w-1/2">
        <div class="flex flex-row justify-between">
          <h3 class="text-lg font-semibold">Edit Book</h3>
          <button @click="closeModal">
            <i class="fa-solid fa-circle-xmark text-red-500"></i>
          </button>
        </div>

        <img
          :src="`/storage/${currentBook.cover}`"
          alt="Book Cover"
          class="w-20 h-20 object-cover rounded-md"
        />
        <InputLabel for="name" value="Book Title" />
        <TextInput
          id="name"
          type="text"
          class="mt-1 block w-full"
          v-model="currentBook.name"
          required
          autofocus
        />

        <InputLabel for="author" value="Author Name" />
        <TextInput
          id="author"
          type="text"
          class="mt-1 block w-full"
          v-model="currentBook.author"
          required
          autofocus
        />

        <button
          type="button"
          @click="showCover"
          class="block text-sm font-medium text-blue-600 hover:text-gray-700"
        >
          Update Book Cover
        </button>
        <div v-if="changeCover === true" class="flex space-x-2">
          <input
            id="cover"
            type="file"
            class="mt-1 block w-full"
            @change="handleFileChange"
            accept=".jpeg,.png,.jpg,.gif"
          />

          <button @click="closeCover">
            <i class="fa-solid fa-circle-xmark text-gray-500"></i>
          </button>
        </div>

        <div class="flex flex-row mt-2 space-x-2">
          <PrimaryButton @click="updateBook"> Update Book Details </PrimaryButton>
          <DangerButton @click="deleteBook"> Delete Book </DangerButton>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
