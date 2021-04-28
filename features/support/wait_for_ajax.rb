module WaitForAjax
  extend self
  
  #Helper Functions
  def wait_for_ajax
    attempts = 10
    while attempts >= 0 and not finished_all_ajax_requests? do
      attempts -= 1
      # It's had 5 seconds to load, maybe it's stuck? Reload and keep trying.
      if attempts % 5 == 0 then
        Capybara.page.evaluate_script("window.location.reload()")
      end
      sleep(1)
    end
  end

  def finished_all_ajax_requests?
    Capybara.page.evaluate_script('document.readyState === "complete" && typeof(jQuery) === "function" && jQuery.active === 0')
  end

  end
  