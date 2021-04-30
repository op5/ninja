module WaitForAjax
  extend self
  
  #Helper Functions
  def wait_for_ajax
    attempts = 5
    while attempts >= 0 and not finished_all_ajax_requests? do
      attempts -= 1
      sleep(1)
    end
  end

  def finished_all_ajax_requests?
    if Capybara.page.evaluate_script('document.readyState === "complete"')
      if Capybara.page.evaluate_script('typeof jQuery == "function"')
        return true
      end
  end

  end
  