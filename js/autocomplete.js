
function autocomplete(input, itemList) {
  let oldList = document.querySelector('.autocomplete > .autocomplete-items');
  if(oldList && input.parentNode.contains(oldList)) {
    oldList.remove();
  }
  
  const autocompleteList = document.createElement('div');
  autocompleteList.classList.add('autocomplete-items');
  input.parentNode.appendChild(autocompleteList);

  let chosenElement = null;

  for(let i = 0; i < itemList.length; i++) {
    let item = itemList[i];
    const divChild = document.createElement('div');
    divChild.innerHTML = item;

    autocompleteList.appendChild(divChild);
  }
  //autocompleteList.childNodes[0].classList.add('autocomplete-active');

}

async function requestUserList(name) {
  return await makeAjaxUserQuery(name);
}

function makeAjaxUserQuery(name) {
  return new Promise((resolve, reject) => {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", `searchUsers.php?name=${name}`, true);
    xhr.onload = () => {
      if(xhr.status >= 200 && xhr.status < 300) {
        resolve(xhr.response);
      } else {
        reject({
          status: xhr.status,
          statusText: xhr.statusText
        });
      }
    };
    xhr.onerror = () => {
      reject({
        status: xhr.status,
        statusText: xhr.statusText
      });
    };
    xhr.send();
  });
}