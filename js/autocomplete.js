var Autocomplete = {

registerInput: (input) => {
  input.addEventListener('keydown', Autocomplete.keyboardElementSwitch, false);
  return null;
},

autocomplete: (input, itemList) => {
  Autocomplete.removeSuggestions(input);
  if(itemList.length <= 0) {
    return;
  }

  const autocompleteList = document.createElement('div');
  autocompleteList.classList.add('autocomplete-items');
  input.parentNode.appendChild(autocompleteList);

  for(let i = 0; i < itemList.length; i++) {
    let item = itemList[i];
    const divChild = document.createElement('div');
    divChild.innerHTML = item;

    autocompleteList.appendChild(divChild);
  }

  autocompleteList.children[0].classList.add('autocomplete-active');
},

keyboardElementSwitch: event => {
  const input = event.currentTarget;
  const goUpKeys = [37, 38];
  const goDownKeys = [39, 40];

  const suggestionsDiv = input.parentNode.querySelector('.autocomplete-items');
  if(suggestionsDiv === null || suggestionsDiv.childNodes === undefined)
    return;

  const suggestions = suggestionsDiv.childNodes;

  let currentSelectedIndex = null;
  for(let i = 0; i < suggestions.length; i++) {
    const suggestion = suggestions[i];
    if(suggestion.classList.contains('autocomplete-active')) {
      currentSelectedIndex = i;
      suggestion.classList.remove('autocomplete-active');
    }
  }

  let nextIndex = 0;

  let requestToChange = false;
  if(goUpKeys.includes(event.keyCode)) {
    if(currentSelectedIndex === null) {
      nextIndex = suggestions.length - 1;
    }
    nextIndex = currentSelectedIndex === 0 ? suggestions.length - 1 : (currentSelectedIndex - 1);
    requestToChange = true;
  }
  if(goDownKeys.includes(event.keyCode)) {
    if(currentSelectedIndex === null) {
      nextIndex = -1;
    }
    nextIndex = (currentSelectedIndex + 1) % suggestions.length;
    requestToChange = true;
  }

  suggestions[nextIndex].classList.add('autocomplete-active');
  if(requestToChange) {
  }

  if(event.keyCode == 27 || event.keyCode == 13) {
    Autocomplete.removeSuggestions(input);
  }
  if(event.keyCode === 13) {
    input.value = suggestions[currentSelectedIndex].innerHTML;
  }
},

removeSuggestions: (input) => {
  let oldList = input.parentNode.querySelector('.autocomplete-items');
  if(oldList && input.parentNode.contains(oldList)) {
    oldList.remove();
  }
}

};
